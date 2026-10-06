<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\DisposalRequest;
use App\Models\Location;
use App\Models\PerolehanAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    // HALAMAN UTAMA: Membuka tabel daftar Aset Aktif lengkap dengan alat saringan (filter) pencariannya.
    public function index(Request $request)
    {
        // 1. Inisialisasi query dasar: hanya aset yang belum dilepas (belum ada disposal request yang disetujui)
        $query = Asset::whereDoesntHave('disposal')->with('perolehanAset');

        // 2. Filter dari DB (Pencarian, Kategori & Ruangan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Pencarian kata utuh (word boundary) agar "AC" tidak me-match "kaca"
                $q->where('nama_aset', 'LIKE', "% {$search} %")
                  ->orWhere('nama_aset', 'LIKE', "{$search} %")
                  ->orWhere('nama_aset', 'LIKE', "% {$search}")
                  ->orWhere('nama_aset', '=', $search)
                  // Pencarian kode aset tetap parsial
                  ->orWhere('kode_aset', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Ambil data dari database
        $assets = $query->latest()->get();

        // 3. Filter Collection (Status Umur Ekonomis dihitung dinamis)
        if ($request->filled('status_umur')) {
            $assets = $assets->filter(function ($asset) use ($request) {
                // attribute status_umur mengembalikan HTML string, kita cari kata kuncinya
                $statusHtml = $asset->status_umur;

                if ($request->status_umur == 'aman') {
                    return str_contains($statusHtml, 'Aman');
                } elseif ($request->status_umur == 'peringatan') {
                    return str_contains($statusHtml, 'Peringatan');
                } elseif ($request->status_umur == 'habis') {
                    return str_contains($statusHtml, 'Habis Umur Ekonomis');
                }

                return true;
            });
        }

        // Data Master untuk Dropdown Filter
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_ruangan')->get();

        // Implementasi Pagination Manual untuk Collection
        $perPage = 10;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $assets = new \Illuminate\Pagination\LengthAwarePaginator(
            $assets->forPage($page, $perPage)->values(),
            $assets->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('assets.index', compact('assets', 'categories', 'locations'));
    }

    // HALAMAN ASET TERBUANG: Membuka khusus daftar barang/aset yang sudah di-ACC untuk dilepas oleh Kepsek.
    public function disposedIndex()
    {
        $assets = Asset::whereHas('disposal')->with(['perolehanAset', 'disposal'])->latest()->paginate(10)->withQueryString();

        $totalDisposedCount = Asset::whereHas('disposal')->count();

        $totalRecoveryValue = \App\Models\DisposalRequest::where('status', 'completed')->sum('harga_jual');

        $assetsDisposed = Asset::whereHas('disposal')->get();

        $totalBukuSisaDisposed = 0;
        foreach ($assetsDisposed as $asset) {
            $totalBukuSisaDisposed += $asset->nilai_buku;
        }

        return view('assets.disposed', compact('assets', 'totalDisposedCount', 'totalRecoveryValue', 'totalBukuSisaDisposed'));
    }

    // FORM TAMBAH ASET: Memanggil lembar putih (formulir) kosong untuk mengetik barang baru.
    public function create()
    {
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_ruangan')->get();
        return view('assets.create', compact('categories', 'locations'));
    }

    // SIMPAN ASET BARU: Menyimpan hasil ketikan dari Formulir Tambah Aset ke dalam brankas Database secara permanen.
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1|max:500',
            'base_kode_aset' => 'nullable|string|max:20',
            'nama_aset' => 'required|string|max:255',
            'merek_aset' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'kondisi' => 'required|string',
            'unit' => 'required|string',
            'vendor_aset' => 'nullable|string',
            'sumber_dana' => 'required|in:Yayasan,BOS,Wakaf,Hibah,Lainnya',
            'harga_perolehan' => 'required|numeric|min:0',
            'umur_ekonomis_tahun' => 'required|integer|min:1',
            'is_rutin_servis' => 'nullable|boolean',
            'interval_servis' => 'nullable|required_if:is_rutin_servis,1|integer|min:1',

            // Kolom Transaksi Masuk Generik
            'tanggal_masuk' => 'required|date',
            'jenis_transaksi_masuk' => 'required|in:Hibah masuk,Pembelian,Pemindahan masuk,Pinjaman,Sewa',
            'is_pihak_kedua_karyawan_masuk' => 'required|boolean',
            'nik_pihak_kedua_masuk' => 'nullable|string|max:255',
            'nama_pihak_kedua_masuk' => 'nullable|string|max:255',
            'alamat_pihak_kedua_masuk' => 'nullable|string',
            'bukti_transaksi_masuk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Upload File Bukti Transaksi
            $buktiPath = null;
            if ($request->hasFile('bukti_transaksi_masuk')) {
                $file = $request->file('bukti_transaksi_masuk');
                $filename = time() . '_masuk_' . $file->getClientOriginalName();
                $buktiPath = $file->storeAs('bukti_transaksi', $filename, 'public');
            }


            $isRutinServis = $validatedData['is_rutin_servis'] ?? false;
            $intervalServis = $isRutinServis ? (int) $validatedData['interval_servis'] : null;
            $tanggalServisTerakhir = $isRutinServis ? now()->toDateString() : null;
            $tanggalServisBerikutnya = $isRutinServis ? \Carbon\Carbon::parse($tanggalServisTerakhir)->addMonths($intervalServis)->toDateString() : null;

            $quantity = (int) $validatedData['quantity'];
            $baseKode = $validatedData['base_kode_aset'] ?? 'EDU';

            $createdCount = 0;
            $startIndex = 1;

            for ($i = 0; $i < $quantity; $i++) {
                $finalKodeAset = null;
                if ($baseKode) {
                    do {
                        $kodeIndex = str_pad($startIndex, 3, "0", STR_PAD_LEFT);
                        $finalKodeAset = "{$baseKode}-{$kodeIndex}";
                        $startIndex++;
                    } while (Asset::where('kode_aset', $finalKodeAset)->exists());
                }

                $namaAset = $quantity > 1 ? $validatedData['nama_aset'] . ' - ' . ($i + 1) : $validatedData['nama_aset'];

                $asset = Asset::create([
                    'kode_aset' => $finalKodeAset,
                    'nama_aset' => $namaAset,
                    'merek_aset' => $validatedData['merek_aset'],
                    'category_id' => $validatedData['category_id'],
                    'location_id' => $validatedData['location_id'] ?? null,
                    'kondisi' => $validatedData['kondisi'],
                    'unit' => $validatedData['unit'],
                    'vendor_aset' => $validatedData['vendor_aset'],
                    'sumber_dana' => $validatedData['sumber_dana'],
                    'harga_perolehan' => $validatedData['harga_perolehan'],
                    'umur_ekonomis_tahun' => $validatedData['umur_ekonomis_tahun'],
                    'is_rutin_servis' => $isRutinServis,
                    'interval_servis' => $intervalServis,
                    'tanggal_servis_terakhir' => $tanggalServisTerakhir,
                    'tanggal_servis_berikutnya' => $tanggalServisBerikutnya,
                ]);

                \App\Models\PerolehanAset::create([
                    'asset_id' => $asset->id,
                    'tanggal_masuk' => $validatedData['tanggal_masuk'],
                    'jenis_transaksi_masuk' => $validatedData['jenis_transaksi_masuk'],
                    'bukti_transaksi_masuk' => $buktiPath,
                    'is_pihak_kedua_karyawan_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'],
                    'nik_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? $validatedData['nik_pihak_kedua_masuk'] : null,
                    'nama_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? null : $validatedData['nama_pihak_kedua_masuk'],
                    'alamat_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? null : $validatedData['alamat_pihak_kedua_masuk'],
                ]);

                $createdCount++;
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('assets.index')->with('success', "Berhasil menambahkan $createdCount data aset ke dalam sistem!");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // TAMPILKAN DETAIL 1 SET LENGKAP: Membuka layar rincian khusus 1 aset tunggal, mencakup riwayat servis hingga pelepasannya.
    public function show(Asset $asset)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'kepala_sekolah' && (!$asset->disposal || is_null($asset->disposal->tanggal_keluar))) {
            abort(403, 'Anda tidak memiliki akses ke detail aset aktif.');
        }

        $asset->load([
            'perolehanAset',
            'disposal',
            'histories.user',
            'maintenanceLogs' => function ($query) {
                $query->orderBy('tanggal_servis', 'desc');
            }
        ]);
        return view('assets.show', compact('asset'));
    }

    // FORM EDIT ASET: Memanggil lembar formulir tapi sengaja diisikan otomatis oleh sistem pakai data lama agar siap dikoreksi.
    public function edit(Asset $asset)
    {
        // Panggil relasi transaksi awal
        $asset->load('perolehanAset');
        $categories = Category::orderBy('nama_kategori')->get();
        $locations = Location::orderBy('nama_ruangan')->get();
        return view('assets.edit', compact('asset', 'categories', 'locations'));
    }

    // SIMPAN HASIL EDITAN: Merekam tulisan/perbaikan data terbaru dan menge-save perubahannya. Termasuk merekam riwayat pergerakan.
    public function update(Request $request, Asset $asset)
    {
        $validatedData = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'merek_aset' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'kondisi' => 'required|string',
            'unit' => 'required|string',
            'vendor_aset' => 'nullable|string',
            'sumber_dana' => 'required|in:Yayasan,BOS,Wakaf,Hibah,Lainnya',
            'harga_perolehan' => 'required|numeric|min:0',
            'umur_ekonomis_tahun' => 'required|integer|min:1',

            // Kolom Tambahan
            'kode_aset' => 'nullable|string|max:255|unique:assets,kode_aset,' . $asset->id,
            'is_rutin_servis' => 'nullable|boolean',
            'interval_servis' => 'nullable|required_if:is_rutin_servis,1|integer|min:1',

            // Transaksi Masuk (Edit)
            'tanggal_masuk' => 'required|date',
            'jenis_transaksi_masuk' => 'required|in:Hibah masuk,Pembelian,Pemindahan masuk,Pinjaman,Sewa',
            'bukti_transaksi_masuk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'is_pihak_kedua_karyawan_masuk' => 'required|boolean',
            'nik_pihak_kedua_masuk' => 'nullable|required_if:is_pihak_kedua_karyawan_masuk,1|string|max:255',
            'nama_pihak_kedua_masuk' => 'nullable|required_if:is_pihak_kedua_karyawan_masuk,0|string|max:255',
            'alamat_pihak_kedua_masuk' => 'nullable|required_if:is_pihak_kedua_karyawan_masuk,0|string'
        ]);

        DB::beginTransaction();
        try {
            // Cek kondisi sebelum update untuk Histori
            $oldRuangan = $asset->location_id;
            $oldKondisi = $asset->kondisi;


            // Logika Servis AC Otomatis
            $isRutinServis = $validatedData['is_rutin_servis'] ?? false;
            $intervalServis = $isRutinServis ? (int) $validatedData['interval_servis'] : null;

            $tanggalServisTerakhir = $asset->tanggal_servis_terakhir;
            $tanggalServisBerikutnya = $asset->tanggal_servis_berikutnya;

            if ($isRutinServis) {
                // Jika belum pernah diservis (baru diaktifkan), ambil dari tanggal masuk.
                // Atau jika ada perubahan, kita bisa menetapkan jadwal baru dari servis terakhir.
                // Untuk kesederhanaan, jika kosong, diisi dari tanggal masuk.
                if (!$tanggalServisTerakhir) {
                    $tanggalServisTerakhir = now()->toDateString();
                }
                // Hitung kembali tanggal servis berikutnya tiap kali submit untuk keakuratan
                $tanggalServisBerikutnya = \Carbon\Carbon::parse($tanggalServisTerakhir)->addMonths($intervalServis)->toDateString();
            }

            // 1. Update Data Master
            $asset->update([
                'kode_aset' => $validatedData['kode_aset'] ?? null,
                'nama_aset' => $validatedData['nama_aset'],
                'merek_aset' => $validatedData['merek_aset'],
                'category_id' => $validatedData['category_id'],
                'location_id' => $validatedData['location_id'] ?? null,
                'kondisi' => $validatedData['kondisi'],
                'unit' => $validatedData['unit'],
                'vendor_aset' => $validatedData['vendor_aset'],
                'sumber_dana' => $validatedData['sumber_dana'],
                'harga_perolehan' => $validatedData['harga_perolehan'],
                'umur_ekonomis_tahun' => $validatedData['umur_ekonomis_tahun'],
                'is_rutin_servis' => $isRutinServis,
                'interval_servis' => $intervalServis,
                'tanggal_servis_terakhir' => $tanggalServisTerakhir,
                'tanggal_servis_berikutnya' => $isRutinServis ? $tanggalServisBerikutnya : null,
            ]);

            // Cek perubahan Mutasi Ruangan
            if ($oldRuangan != $validatedData['location_id']) {
                $oldLocName = $oldRuangan ? Location::find($oldRuangan)->nama_ruangan : '-';
                $newLocName = $validatedData['location_id'] ? Location::find($validatedData['location_id'])->nama_ruangan : '-';

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'jenis_perubahan' => 'Mutasi Ruangan',
                    'nilai_lama' => $oldLocName,
                    'nilai_baru' => $newLocName,
                    'keterangan' => 'Perpindahan ruangan secara manual saat edit aset.'
                ]);
            }

            // Cek perubahan Kondisi Aset
            if ($oldKondisi !== $validatedData['kondisi']) {
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'jenis_perubahan' => 'Perubahan Kondisi',
                    'nilai_lama' => $oldKondisi,
                    'nilai_baru' => $validatedData['kondisi'],
                    'keterangan' => 'Pembaruan kondisi fisik secara manual saat edit aset.'
                ]);
            }

            // 2. Update Data Relasi Transaksi
            if ($asset->perolehanAset) {
                // Upload File Masuk (hanya jika ada upload baru)
                $buktiMasukPath = $asset->perolehanAset->bukti_transaksi_masuk;
                if ($request->hasFile('bukti_transaksi_masuk')) {
                    $file = $request->file('bukti_transaksi_masuk');
                    $filename = time() . '_masuk_' . $file->getClientOriginalName();
                    $buktiMasukPath = $file->storeAs('bukti_transaksi', $filename, 'public');
                }

                $asset->perolehanAset->update([
                    'tanggal_masuk' => $validatedData['tanggal_masuk'],
                    'jenis_transaksi_masuk' => $validatedData['jenis_transaksi_masuk'],
                    'bukti_transaksi_masuk' => $buktiMasukPath,
                    'is_pihak_kedua_karyawan_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'],
                    'nik_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? $validatedData['nik_pihak_kedua_masuk'] : null,
                    'nama_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? null : $validatedData['nama_pihak_kedua_masuk'],
                    'alamat_pihak_kedua_masuk' => $validatedData['is_pihak_kedua_karyawan_masuk'] ? null : $validatedData['alamat_pihak_kedua_masuk']
                ]);
            }

            DB::commit();
            return redirect()->route('assets.index')->with('success', 'Perubahan data aset berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }


    // SAHKAN SERVIS RUTIN: Ketika tombol 'Selesai Servis' diklik, fungsinya mencatat tanggal servis hari ini sekaligus memajukan kalender jadwal servis berikutnya (next date) otomatis!
    public function completeService(Request $request, Asset $asset)
    {
        if (!$asset->is_rutin_servis || !$asset->interval_servis) {
            return back()->with('error', 'Aset ini tidak dikonfigurasi untuk servis rutin.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $biaya = $request->input('biaya_servis', 0);

            // Bersihkan jadwal log sebelumnya agar tidak muncul kembali di peringatan
            \App\Models\MaintenanceLog::where('asset_id', $asset->id)
                ->whereNotNull('next_service_date')
                ->update(['next_service_date' => null]);

            // Catat ke maintenance log
            \App\Models\MaintenanceLog::create([
                'asset_id' => $asset->id,
                'tanggal_servis' => now()->toDateString(),
                'deskripsi_kerusakan' => 'Servis Rutin/Berkala otomatis tersistem',
                'biaya_perbaikan' => $biaya,
                'next_service_date' => now()->addMonths($asset->interval_servis)->toDateString(),
            ]);

            // Update Aset
            $asset->update([
                'tanggal_servis_terakhir' => now()->toDateString(),
                'tanggal_servis_berikutnya' => now()->addMonths($asset->interval_servis)->toDateString(),
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Servis rutin untuk aset ' . $asset->nama_aset . ' telah dicatat sebagai selesai dan jadwal berikutnya telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penyelesaian servis: ' . $e->getMessage());
        }
    }

    // KIRIM SURAT PENGAJUAN BUANG: Tombol dari modal Buang Aset ngelempar data ke sistem supaya  menunggu Approval Kepsek.
    public function dispose(Request $request, Asset $asset)
    {
        $validatedData = $request->validate([
            'alasan_pengajuan' => 'required|string',
            'foto_kondisi' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            if ($asset->hasPendingDisposalRequest()) {
                return back()->with('error', 'Aset ini sudah memiliki pengajuan pelepasan yang sedang diproses.');
            }

            $fotoPath = null;
            if ($request->hasFile('foto_kondisi')) {
                $file = $request->file('foto_kondisi');
                $filename = time() . '_kondisi_' . $file->getClientOriginalName();
                $fotoPath = $file->storeAs('disposal_requests', $filename, 'public');
            }

            DisposalRequest::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'requester_name' => auth()->user()->name,
                'requester_role' => auth()->user()->role,
                'alasan_pengajuan' => $request->alasan_pengajuan,
                'foto_kondisi' => $fotoPath,
                'status' => 'pending'
            ]);

            return redirect()->route('assets.index')->with('success', 'Pengajuan pelepasan aset berhasil dikirim dan menunggu persetujuan Kepala Sekolah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengajukan pelepasan aset: ' . $e->getMessage());
        }
    }

    // PERPANJANGAN UMUR LAYAK PAKAI: Aset yang menyusut ke angka 0% (Habis) namun fisiknya masih berfungsi kuat, sanggup dicap "Masih Layak" oleh Admin lewat tuas/tombol ini!
    public function verifyLayak(Asset $asset)
    {
        try {
            $asset->update(['is_layak_pakai' => true]);

            \App\Models\AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'tanggal' => now()->toDateString(),
                'jenis_perubahan' => 'Verifikasi Kelayakan',
                'nilai_lama' => 'Habis Umur Ekonomis',
                'nilai_baru' => 'Habis Umur - Terverifikasi Layak',
                'keterangan' => 'Aset telah dicek fisik dan diverifikasi masih layak pakai.'
            ]);

            return back()->with('success', 'Aset berhasil diverifikasi sebagai masih layak pakai.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memverifikasi kelayakan aset: ' . $e->getMessage());
        }
    }

    // HAPUS PERMANEN ASET: Menghapus data tersebut dan hilang di semua tabel database! (Hati-hati).
    public function destroy(Asset $asset)
    {
        try {
            // Data transaksi & maintenance akan terhapus logis krn onDelete('cascade') di migration
            $asset->delete();
            return redirect()->route('assets.index')->with('success', 'Aset telah dihapus dari sistem manajemen secara permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }

    // ALAT BANTU KALENDER: Ini adalah asisten tersembunyi yang bertugas menerjemahkan pilihan kalender Bapak/Ibu Bendahara jadi format yg dibaca komputer.
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

    // CETAK EXCEL ASET AKTIF: Tugas yang merangkum aset lalu mensupply-nya ke mesin pencetak format .XLSX!
    public function exportExcel(Request $request)
    {
        $sumberDana = $request->query('sumber_dana', 'Semua');
        [$startDate, $endDate] = $this->resolveDates($request);

        $namaFile = 'Laporan_Aset_' . $sumberDana . '_' . date('Ymd') . '.xlsx';

        return (new AssetsExport($sumberDana, $startDate, $endDate))->download($namaFile);
    }

    // CETAK PDF ASET AKTIF: Sama seperti di atas, tapi diarahkan ke mesin cetak dokumen kertas (PDF).
    public function exportPdf(Request $request)
    {
        $sumberDana = $request->query('sumber_dana', 'Semua');
        [$startDate, $endDate] = $this->resolveDates($request);

        $namaFile = 'Laporan_Aset_' . $sumberDana . '_' . date('Ymd');
        $labelPeriode = "Sistem Keseluruhan"; // Label khusus dashboard

        if ($startDate && $endDate) {
            $labelPeriode = "Periode " . \Carbon\Carbon::parse($startDate)->format('dM-Y') . " sd " . \Carbon\Carbon::parse($endDate)->format('dM-Y');
        }

        $exportClass = new AssetsExport($sumberDana, $startDate, $endDate);
        $assets = $exportClass->getAssets();
        $totals = method_exists($exportClass, 'getTotals') ? $exportClass->getTotals() : null;
        $jenisLaporan = 'daftar_aset';

        $pdf = app('dompdf.wrapper')->loadView('reports.pdf', compact('assets', 'labelPeriode', 'sumberDana', 'totals', 'jenisLaporan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile . '.pdf');
    }

    // CETAK EXCEL ASET LEPAS: Mesin ekspor Excel namun target korbannya 100% adalah aset-aset yang sudah divalidasi terjual atau dibuang!
    public function exportDisposedExcel(Request $request)
    {
        $sumberDana = $request->query('sumber_dana', 'Semua');
        [$startDate, $endDate] = $this->resolveDates($request);

        $namaFile = 'Laporan_Aset_Dilepas_' . $sumberDana . '_' . date('Ymd') . '.xlsx';

        return (new AssetsExport($sumberDana, $startDate, $endDate, 'daftar_aset', true))->download($namaFile);
    }

    // CETAK PDF ASET LEPAS: Sama halnya seperti yang di atas, mengubah rekapan barang lepas (rongsok) jadi file laporan resmi PDF!
    public function exportDisposedPdf(Request $request)
    {
        $sumberDana = $request->query('sumber_dana', 'Semua');
        [$startDate, $endDate] = $this->resolveDates($request);

        $namaFile = 'Laporan_Aset_Dilepas_' . $sumberDana . '_' . date('Ymd');
        $labelPeriode = "Sistem Keseluruhan";
        if ($startDate && $endDate) {
            $labelPeriode = "Periode " . \Carbon\Carbon::parse($startDate)->format('dM-Y') . " sd " . \Carbon\Carbon::parse($endDate)->format('dM-Y');
        }

        $exportClass = new AssetsExport($sumberDana, $startDate, $endDate, 'aset_lepas', true);
        $assets = $exportClass->getAssets();
        $totals = method_exists($exportClass, 'getTotals') ? $exportClass->getTotals() : null;
        $jenisLaporan = 'aset_lepas';

        $pdf = app('dompdf.wrapper')->loadView('reports.pdf', compact('assets', 'labelPeriode', 'sumberDana', 'totals', 'jenisLaporan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($namaFile . '.pdf');
    }
}

