<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // HALAMAN DEPAN (DASHBOARD): Otak perakit halaman depan. Dia membaca seluruh kalender alarm servis, umur barang, nilai kekayaan sekolah, lalu menjadikannya grafik & angka ringkas!
    public function dashboard()
    {
        $hariIni = Carbon::now();
        // Tampilkan peringatan servis dalam 31 hari ke depan agar cover 1 bulan penuh
        $batasWaktuWarning = Carbon::now()->addDays(31);
        $earlyWarnings = MaintenanceLog::with('asset')
            ->whereNotNull('next_service_date')
            ->whereHas('asset', function ($query) {
                // Hanya aset aktif (belum dilepas)
                $query->whereDoesntHave('disposal')
                    ->where('is_rutin_servis', false);
            })
            ->where('next_service_date', '>=', Carbon::now()->subDays(7)->startOfDay()) // Termasuk yang overdue 7 hari
            ->where('next_service_date', '<=', $batasWaktuWarning)
            ->orderBy('next_service_date', 'asc')
            ->get();

        // Tampilkan peringatan servis rutin mingguan (AC dll)
        // HANYA aset yang belum dilepas (Disinkronkan dengan 31 hari ke depan)
        $batasWaktuServisRutin = Carbon::now()->addDays(31);
        $rutinServiceWarnings = \App\Models\Asset::where('is_rutin_servis', true)
            ->whereNotNull('tanggal_servis_berikutnya')
            ->where('tanggal_servis_berikutnya', '<=', $batasWaktuServisRutin)
            ->whereDoesntHave('disposal')
            ->orderBy('tanggal_servis_berikutnya', 'asc')
            ->get();

        // Tampilkan peringatan umur ekonomis yang akan habis dalam <= 3 bulan (90 hari)
        // Hanya aset aktif (belum dilepas)
        $assets = \App\Models\Asset::with('perolehanAset')
            ->whereDoesntHave('disposal')->get();
        $expiringAssets = [];

        foreach ($assets as $asset) {
            if ($asset->perolehanAset && $asset->perolehanAset->tanggal_masuk) {
                $tanggalBeli = Carbon::parse($asset->perolehanAset->tanggal_masuk);
                $tanggalBatasUmur = $tanggalBeli->copy()->addYears($asset->umur_ekonomis_tahun);

                // Cari sisa hari
                $sisaHari = Carbon::now()->diffInDays($tanggalBatasUmur, false); // false = return negative if past

                // Jika umur <= 90 hari atau sudah minus (habis) dan BELUM diverifikasi layak pakai
                if ($sisaHari <= 90 && !$asset->is_layak_pakai) {
                    $asset->sisa_hari_umur = $sisaHari;
                    $expiringAssets[] = $asset;
                }
            }
        }

        // Sorting yang paling mendesak (paling kecil sisa harinya)
        usort($expiringAssets, function ($a, $b) {
            return $a->sisa_hari_umur <=> $b->sisa_hari_umur;
        });

        // Menghitung Kondisi Aset Aktif (Belum Dilepas)
        // Aset aktif = tidak ada transaksi tanggal_keluar null
        $activeAssets = \App\Models\Asset::whereDoesntHave('disposal')->get();

        $layakPakaiCount = $activeAssets->whereIn('kondisi', ['Baik', 'baik', 'Rusak Ringan', 'rusak ringan'])->count();
        $rusakCount = $activeAssets->whereIn('kondisi', ['Rusak Berat', 'rusak berat'])->count();

        // Total Nilai Kekayaan Aset
        $semuaAset = \App\Models\Asset::with('disposal')->get();
        $totalNilaiAset = 0;
        foreach ($semuaAset as $ast) {
            if ($ast->disposal && $ast->disposal->tanggal_keluar) {
                // Aset Dilepas -> Kontribusi 0 (Kekayaan berkurang sebesar harga perolehan aset tersebut)
            } else {
                // Aset Aktif -> Berkontribusi penuh pada Gross Asset (Harga Beli Awal)
                $totalNilaiAset += $ast->harga_perolehan;
            }
        }

        $totalUnitAktif = $activeAssets->count();

        // Hitung pengajuan pelepasan aset yang menunggu persetujuan Kepsek
        $pendingDisposalsCount = \App\Models\DisposalRequest::where('status', 'pending')->count();

        return view('dashboard', compact('earlyWarnings', 'rutinServiceWarnings', 'expiringAssets', 'layakPakaiCount', 'rusakCount', 'totalNilaiAset', 'totalUnitAktif', 'pendingDisposalsCount'));
    }
}
