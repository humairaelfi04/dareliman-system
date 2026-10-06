<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use App\Exports\MaintenanceLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    // HALAMAN UTAMA RIWAYAT SERVIS: Menampilkan tabel besar yang berisi daftar catatan perbaikan seluruh barang. 
    public function index()
    {
        $logs = MaintenanceLog::with('asset')->orderBy('tanggal_servis', 'desc')->paginate(10);
        $totalBiaya = MaintenanceLog::sum('biaya_perbaikan');

        $activeAssetsCount = \App\Models\Asset::whereDoesntHave('disposal')->count();

        // Menghitung jadwal untuk aset yang belum dilepas
        // Menggunakan startOfDay() agar jadwal hari ini ikut terhitung
        $next30DaysSchedulesCount = MaintenanceLog::whereNotNull('next_service_date')
            ->where('next_service_date', '>=', now()->startOfDay())
            ->where('next_service_date', '<=', now()->addDays(31)->endOfDay())
            ->whereHas('asset', function ($q) {
                $q->whereDoesntHave('disposal');
            })->count();

        // Tambahkan juga dari Asset yang is_rutin_servis
        $next30DaysSchedulesCount += \App\Models\Asset::where('is_rutin_servis', true)
            ->whereNotNull('tanggal_servis_berikutnya')
            ->where('tanggal_servis_berikutnya', '>=', now()->startOfDay())
            ->where('tanggal_servis_berikutnya', '<=', now()->addDays(31)->endOfDay())
            ->whereDoesntHave('disposal')->count();

        $urgentSchedulesCount = MaintenanceLog::whereNotNull('next_service_date')
            ->whereBetween('next_service_date', [now()->subDays(60), now()->addDays(7)])
            ->whereHas('asset', function ($q) {
                $q->whereDoesntHave('disposal');
            })->count();

        // Tambahkan juga dari Asset yang is_rutin_servis
        $urgentSchedulesCount += \App\Models\Asset::where('is_rutin_servis', true)
            ->whereNotNull('tanggal_servis_berikutnya')
            ->whereBetween('tanggal_servis_berikutnya', [now()->subDays(60), now()->addDays(7)])
            ->whereDoesntHave('disposal')->count();

        return view('maintenance.index', compact('logs', 'totalBiaya', 'activeAssetsCount', 'next30DaysSchedulesCount', 'urgentSchedulesCount'));
    }

    // ALAT BANTU KALENDER: Fungsi yang membaca format kalender khusus untuk tombol saringan laporan bulanan/tahunan.
    private function resolveDates(\Illuminate\Http\Request $request)
    {
        $periode = $request->query('periode', 'kustom');
        $tahun = $request->query('tahun', date('Y'));

        $startDate = null;
        $endDate = null;

        if ($periode == 'bulanan') {
            $bulan = $request->query('bulan', date('m'));
            $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        } elseif ($periode == 'tahunan') {
            $startDate = \Carbon\Carbon::createFromDate($tahun, 1, 1)->startOfYear();
            $endDate = \Carbon\Carbon::createFromDate($tahun, 12, 31)->endOfYear();
        } elseif ($periode == 'kustom') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = \Carbon\Carbon::parse($request->query('start_date'))->startOfDay();
                $endDate = \Carbon\Carbon::parse($request->query('end_date'))->endOfDay();
            }
        }
        return [$startDate, $endDate];
    }

    // CETAK EXCEL: Tombol untuk memanggil cetak format Microsoft Excel.
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDates($request);
        $namaFile = 'Riwayat_Pemeliharaan_Aset_' . date('Ymd') . '.xlsx';
        return Excel::download(new MaintenanceLogsExport($startDate, $endDate), $namaFile);
    }

    // CETAK PDF: Memasukkan rekapan riwayat ke dalam mesin PDF pembuat laporan kertas murni.
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDates($request);

        $namaFile = 'Riwayat_Pemeliharaan_Aset_' . date('Ymd');
        $labelPeriode = "Sepanjang Waktu";

        if ($startDate && $endDate) {
            $labelPeriode = "Periode " . \Carbon\Carbon::parse($startDate)->format('d M Y') . " sd " . \Carbon\Carbon::parse($endDate)->format('d M Y');
        }

        $exportClass = new MaintenanceLogsExport($startDate, $endDate);
        $logs = $exportClass->getAssets();
        $totals = $exportClass->getTotals();

        $pdf = app('dompdf.wrapper')->loadView('maintenance.pdf', compact('logs', 'labelPeriode', 'totals'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($namaFile . '.pdf');
    }

    // CATAT SERVIS BARU: Menge-save hasil inputan biaya servis ke dalam database, lalu kalau asetnya butuh servis berkala, dia langsung otomatis menghitung kapan jadwal servis selanjutnya!
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'tanggal_servis' => 'required|date',
            'deskripsi_kerusakan' => 'required|string',
            'biaya_perbaikan' => 'required|numeric|min:0',
            'next_service_date' => 'nullable|date|after_or_equal:tanggal_servis',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Cek apakah aset ini merupakan aset servis rutin yang punya interval
            $asset = Asset::find($validatedData['asset_id']);
            $nextServiceDate = $validatedData['next_service_date'] ?? null;

            if (empty($nextServiceDate) && $asset && $asset->is_rutin_servis && $asset->interval_servis > 0) {
                // Secara otomatis tambahkan bulan sesuai interval
                $nextServiceDate = \Carbon\Carbon::parse($validatedData['tanggal_servis'])
                    ->addMonths($asset->interval_servis)
                    ->toDateString();

                // Masukkan ke validatedData supaya tersimpan di MaintenanceLog
                $validatedData['next_service_date'] = $nextServiceDate;
            }

            // Bersihkan jadwal next_service_date di log sebelumnya agar hilang dari radar dashboard
            MaintenanceLog::where('asset_id', $validatedData['asset_id'])
                ->whereNotNull('next_service_date')
                ->update(['next_service_date' => null]);

            MaintenanceLog::create($validatedData);

            // Permintaan perbaruan jadwal servis berikutnya pada master aset
            if (!empty($nextServiceDate)) {
                $asset->update([
                    'tanggal_servis_terakhir' => $validatedData['tanggal_servis'],
                    'tanggal_servis_berikutnya' => $nextServiceDate,
                ]);
            } else {
                // Jika tidak ada jadwal berikutnya (opsional)
                $asset->update([
                    'tanggal_servis_terakhir' => $validatedData['tanggal_servis'],
                ]);
            }

            // Update kondisi aset jika diberikan
            if ($request->has('kondisi_setelah_servis')) {
                $oldKondisi = $asset->kondisi;
                $newKondisi = $request->kondisi_setelah_servis;

                if ($oldKondisi !== $newKondisi) {
                    $asset->update(['kondisi' => $newKondisi]);

                    // Rekam ke AssetHistory
                    \App\Models\AssetHistory::create([
                        'asset_id' => $asset->id,
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'tanggal' => now()->toDateString(),
                        'jenis_perubahan' => 'Perbaikan/Servis',
                        'nilai_lama' => $oldKondisi,
                        'nilai_baru' => $newKondisi,
                        'keterangan' => 'Kondisi diperbarui setelah servis (' . $validatedData['deskripsi_kerusakan'] . ')'
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Jika request dari dashboard (referer mengandung /dashboard), kembali ke dashboard
            $from = request()->headers->get('referer', '');
            if (str_contains($from, '/dashboard')) {
                return redirect()->route('dashboard')->with('success', 'Servis berhasil dicatat dan hilang dari peringatan dasbor.');
            }
            return back()->with('success', 'Riwayat pemeliharaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyimpan data pemeliharaan: ' . $e->getMessage());
        }
    }


}
