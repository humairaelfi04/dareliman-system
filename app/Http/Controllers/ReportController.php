<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Exports\MaintenanceLogsExport;
use App\Models\Asset;
use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // MENU LAPORAN: Berfungsi sebatas untuk memanggil halaman formulir "Filter Laporan Periodik" (kalender pilihan) ke layar monitor.
    public function index()
    {
        return view('reports.index');
    }

    // KACA PEMBESAR (PREVIEW): Asisten super ngebut yang bertugas menampilkan "pratinjau" tabel ke layar web seketika Anda merubah kalender, sebelum Anda klik Download!
    public function preview(Request $request)
    {
        $periode = $request->input('periode', 'tahunan');
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('m'));
        $startDate = null;
        $endDate = null;
        $labelPeriode = '';

        if ($periode === 'bulanan') {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            $labelPeriode = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
        } elseif ($periode === 'tahunan') {
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear();
            $labelPeriode = "Tahun $tahun";
        } elseif ($periode === 'kustom') {
            $sd = $request->input('start_date');
            $ed = $request->input('end_date');
            if ($sd && $ed) {
                $startDate = Carbon::parse($sd)->startOfDay();
                $endDate = Carbon::parse($ed)->endOfDay();
                $labelPeriode = Carbon::parse($sd)->format('d M Y') . ' s/d ' . Carbon::parse($ed)->format('d M Y');
            } else {
                $labelPeriode = 'Semua Periode';
            }
        }

        $sumberDana = $request->input('sumber_dana', 'Semua');
        // inclusions: comma-separated string e.g. "aset_aktif,pemeliharaan"
        $inclusions = explode(',', $request->input('inclusions', 'aset_aktif'));

        $result = [
            'label_periode' => $labelPeriode,
            'sumber_dana' => $sumberDana,
            'inclusions' => $inclusions,
        ];

        // ── Aset Aktif ──────────────────────────────────────────────
        if (in_array('aset_aktif', $inclusions)) {
            $exportAktif = new AssetsExport($sumberDana, $startDate, $endDate, 'daftar_aset', false);
            $assetsAktif = $exportAktif->getAssets();

            $totals = $exportAktif->getTotals();

            // Per-category breakdown
            $byCategory = [];
            foreach ($assetsAktif as $a) {
                $cat = $a->category->nama_kategori ?? 'Tidak Terkategori';
                if (!isset($byCategory[$cat])) {
                    $byCategory[$cat] = ['jumlah' => 0, 'nilai' => 0, 'kondisi_list' => []];
                }
                $byCategory[$cat]['jumlah']++;
                $byCategory[$cat]['nilai'] += $a->harga_perolehan;
                $byCategory[$cat]['kondisi_list'][] = $a->kondisi ?? '-';
            }
            $kategoris = [];
            foreach ($byCategory as $cat => $data) {
                $kondisiCount = array_count_values($data['kondisi_list']);
                arsort($kondisiCount);
                $kondisi = key($kondisiCount) ?? '-';
                $kategoris[] = [
                    'kategori' => $cat,
                    'jumlah' => $data['jumlah'],
                    'nilai' => $data['nilai'],
                    'kondisi' => $kondisi,
                ];
            }

            // Sample rows (first 5)
            $sampleRows = $assetsAktif->take(5)->map(function ($a) {
                return [
                    'nama' => $a->nama_aset,
                    'kode' => $a->kode_aset ?? '-',
                    'kategori' => $a->category->nama_kategori ?? '-',
                    'kondisi' => $a->kondisi,
                    'nilai' => $a->harga_perolehan,
                ];
            })->values()->toArray();

            $result['aset_aktif'] = [
                'total' => $assetsAktif->count(),
                'total_nilai' => $totals['total_harga_perolehan'],
                'total_nilai_buku' => $totals['total_nilai_buku'],
                'kategoris' => $kategoris,
                'sample_rows' => $sampleRows,
            ];
        }

        // ── Pemeliharaan ─────────────────────────────────────────────
        if (in_array('pemeliharaan', $inclusions)) {
            $exportMaint = new MaintenanceLogsExport($startDate, $endDate);
            $logs = $exportMaint->getAssets();

            $totalBiaya = $logs->sum('biaya_perbaikan');

            $sampleLogs = $logs->take(5)->map(function ($l) {
                return [
                    'aset' => $l->asset->nama_aset ?? '-',
                    'tanggal' => $l->tanggal_servis ? Carbon::parse($l->tanggal_servis)->format('d M Y') : '-',
                    'deskripsi' => \Illuminate\Support\Str::limit($l->deskripsi_kerusakan, 45),
                    'biaya' => $l->biaya_perbaikan,
                ];
            })->values()->toArray();

            $result['pemeliharaan'] = [
                'total' => $logs->count(),
                'total_biaya' => $totalBiaya,
                'sample_rows' => $sampleLogs,
            ];
        }

        // ── Aset Dilepas ─────────────────────────────────────────────
        if (in_array('aset_lepas', $inclusions)) {
            $exportLepas = new AssetsExport($sumberDana, $startDate, $endDate, 'pelepasan_aset', true);
            $assetsLepas = $exportLepas->getAssets();

            $totalHargaJual = $assetsLepas->sum(fn($a) => $a->disposal->harga_jual ?? 0);

            $sampleRows = $assetsLepas->take(5)->map(function ($a) {
                return [
                    'nama' => $a->nama_aset,
                    'tgl_keluar' => $a->disposal && $a->disposal->tanggal_keluar
                        ? Carbon::parse($a->disposal->tanggal_keluar)->format('d M Y')
                        : '-',
                    'jenis' => $a->disposal->jenis_transaksi_keluar ?? '-',
                    'harga_jual' => $a->disposal->harga_jual ?? 0,
                ];
            })->values()->toArray();

            $result['aset_lepas'] = [
                'total' => $assetsLepas->count(),
                'total_harga_jual' => $totalHargaJual,
                'sample_rows' => $sampleRows,
            ];
        }
        // ── Total Aset ─────────────────────────────────────────────
        if (in_array('total_aset', $inclusions)) {
            $exportTotal = new AssetsExport($sumberDana, $startDate, $endDate, 'total_aset', false);
            $assetsTotal = $exportTotal->getAssets();
            $totals = $exportTotal->getTotals();

            $byCategory = [];
            foreach ($assetsTotal as $a) {
                $cat = $a->category->nama_kategori ?? 'Tidak Terkategori';
                if (!isset($byCategory[$cat])) {
                    $byCategory[$cat] = ['jumlah' => 0, 'nilai' => 0];
                }
                $byCategory[$cat]['jumlah']++;
                $byCategory[$cat]['nilai'] += $a->harga_perolehan;
            }
            $kategoris = [];
            foreach ($byCategory as $cat => $data) {
                $kategoris[] = [
                    'kategori' => $cat,
                    'jumlah' => $data['jumlah'],
                    'nilai' => $data['nilai'],
                ];
            }

            $result['total_aset'] = [
                'total' => $assetsTotal->count(),
                'total_nilai' => $totals['total_harga_perolehan'],
                'total_nilai_buku' => $totals['total_nilai_buku'],
                'kategoris' => $kategoris,
            ];
        }

        // ── Penyusutan Aset ─────────────────────────────────────────
        if (in_array('penyusutan_aset', $inclusions)) {
            $exportPenyusutan = new AssetsExport($sumberDana, $startDate, $endDate, 'penyusutan_aset', false);
            $assetsPenyusutan = $exportPenyusutan->getAssets();
            $totalsP = $exportPenyusutan->getTotals();

            $sampleRows = $assetsPenyusutan->take(5)->map(function ($a) {
                return [
                    'nama' => $a->nama_aset,
                    'harga_perolehan' => $a->harga_perolehan,
                    'penyusutan_periode' => $a->calculated_penyusutan_periode ?? 0,
                    'akumulasi' => $a->calculated_akumulasi ?? 0,
                    'nilai_buku' => $a->calculated_nilai_buku ?? 0,
                ];
            })->values()->toArray();

            $result['penyusutan_aset'] = [
                'total' => $assetsPenyusutan->count(),
                'total_penyusutan_periode' => $totalsP['total_penyusutan_periode'] ?? 0,
                'total_akumulasi' => $totalsP['total_akumulasi_penyusutan'] ?? 0,
                'total_nilai_buku' => $totalsP['total_nilai_buku'] ?? 0,
                'sample_rows' => $sampleRows,
            ];
        }
        return response()->json($result);
    }

    // TOMBOL CETAK TOTAL: Otak pengatur yang bekerja saat tombol 'Ekspor/Unduh' diklik. Dia memilih format apa yg di-download (Jadi EXCEL ber-tab banyak atau gabungan lembar PDF).
    public function export(Request $request)
    {
        $periode = $request->input('periode', 'tahunan'); // 'bulanan', 'semester', 'tahunan'
        $tahun = $request->input('tahun', date('Y'));
        $format = $request->input('format', 'excel'); // 'excel' or 'pdf'

        $startDate = null;
        $endDate = null;
        $labelPeriode = '';

        if ($periode == 'bulanan') {
            $bulan = $request->input('bulan', date('m'));
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            $labelPeriode = "Bulan_" . $bulan . "_" . $tahun;
        } elseif ($periode == 'tahunan') {
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear();
            $labelPeriode = "Tahun_" . $tahun;
        } elseif ($periode == 'kustom') {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $labelPeriode = "Kustom_" . $startDate->format('dmY') . "_" . $endDate->format('dmY');

            // Override UI labels untuk di pdf
            $labelPeriodeHeader = "Periode: " . $startDate->format('d M Y') . " s/d " . $endDate->format('d M Y');
        }

        $sumberDana = $request->input('sumber_dana', 'Semua');
        $jenisLaporan = $request->input('jenis_laporan', 'daftar_aset');

        $namaLaporan = "Laporan_Aset";
        if ($jenisLaporan === 'kondisi_aset')
            $namaLaporan = "Laporan_Kondisi_Aset";
        if ($jenisLaporan === 'nilai_aset')
            $namaLaporan = "Laporan_Nilai_Aset";

        $filename = "{$namaLaporan}_SDIT2_DarElIman_{$labelPeriode}";
        if ($sumberDana !== 'Semua') {
            $filename = "{$namaLaporan}_{$sumberDana}_SDIT2_{$labelPeriode}";
        }

        if ($format == 'excel') {
            $inclusionsStr = $request->input('inclusions', 'aset_aktif');
            $inclusions = explode(',', $inclusionsStr);
            return Excel::download(new \App\Exports\MasterReportExport($sumberDana, $startDate, $endDate, $inclusions), $filename . '_MasterLengkap.xlsx');
        }

        $viewData = [
            'labelPeriode' => isset($labelPeriodeHeader) ? $labelPeriodeHeader : $labelPeriode,
            'sumberDana' => $sumberDana
        ];

        if ($format === 'pdf') {
            $inclusionsStr = $request->input('inclusions', 'aset_aktif');
            $inclusions = explode(',', $inclusionsStr);

            if (count($inclusions) > 1) {
                $pdfTitle = 'Laporan Konsolidasi Buku Besar Aset (Master Report)';
            } else {
                $inc = $inclusions[0];
                if ($inc === 'aset_aktif')
                    $pdfTitle = 'Daftar Inventaris Aset Keseluruhan (Aktif)';
                elseif ($inc === 'pemeliharaan')
                    $pdfTitle = 'Laporan Riwayat Servis';
                elseif ($inc === 'aset_lepas' || $inc === 'pelepasan_aset')
                    $pdfTitle = 'Daftar Inventaris Aset Dilepas';
                elseif ($inc === 'penyusutan_aset')
                    $pdfTitle = 'Laporan Penyusutan Aset';
                else
                    $pdfTitle = 'Laporan Data Aset';
            }

            $exportAktif = in_array('aset_aktif', $inclusions) ? new AssetsExport($sumberDana, $startDate, $endDate, 'daftar_aset', false) : null;
            $exportTotal = in_array('total_aset', $inclusions) ? new AssetsExport($sumberDana, $startDate, $endDate, 'total_aset', false) : null;
            $exportPenyusutan = in_array('penyusutan_aset', $inclusions) ? new AssetsExport($sumberDana, $startDate, $endDate, 'penyusutan_aset', false) : null;
            $exportLepas = (in_array('aset_lepas', $inclusions) || in_array('pelepasan_aset', $inclusions)) ? new AssetsExport($sumberDana, $startDate, $endDate, 'pelepasan_aset', true) : null;
            $exportServis = in_array('pemeliharaan', $inclusions) ? new MaintenanceLogsExport($startDate, $endDate) : null;

            $pdfContent = Pdf::loadView('reports.master_pdf', array_merge([
                'title' => $pdfTitle,
                'dataAktif' => $exportAktif ? $exportAktif->getAssets() : null,
                'dataTotal' => $exportTotal ? $exportTotal->getAssets() : null,
                'totalsTotal' => $exportTotal ? $exportTotal->getTotals() : null,
                'dataPenyusutan' => $exportPenyusutan ? $exportPenyusutan->getAssets() : null,
                'totalsPenyusutan' => $exportPenyusutan ? $exportPenyusutan->getTotals() : null,
                'dataLepas' => $exportLepas ? $exportLepas->getAssets() : null,
                'dataServis' => $exportServis ? $exportServis->getAssets() : null,
                'inclusions' => $inclusions
            ], $viewData))->setPaper('a4', 'landscape');
            return $pdfContent->download($filename . '_Laporan.pdf');
        }

        return redirect()->back()->with('error', 'Format tidak didukung');
    }
}
