<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssetsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    protected $sumberDana;
    protected $startDate;
    protected $endDate;
    protected $jenisLaporan;
    protected $isDisposed;

    public function __construct($sumberDana, $startDate = null, $endDate = null, $jenisLaporan = 'daftar_aset', $isDisposed = false)
    {
        $this->sumberDana = $sumberDana;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->jenisLaporan = $jenisLaporan;
        $this->isDisposed = $isDisposed;
    }

    public function getAssets()
    {
        $query = Asset::with(['perolehanAset', 'disposal', 'category', 'location']);

        if ($this->sumberDana != 'Semua') {
            $query->where('sumber_dana', $this->sumberDana);
        }

        if ($this->isDisposed) {
            $query->whereHas('disposal');
            // Filter ketat berdasarkan jangkauan tanggal pelepasan (keluar)
            if ($this->startDate && $this->endDate) {
                $query->whereHas('disposal', function ($q) {
                    $q->whereBetween('tanggal_keluar', [$this->startDate, $this->endDate]);
                });
            }
        } else if ($this->jenisLaporan === 'total_aset' || $this->jenisLaporan === 'penyusutan_aset') {
            // Kondisi saat akhir periode (endDate): aset dibeli sebelum batas akhir, DAN (belum dilepas ATAU dilepas setelah batas akhir)
            if ($this->endDate) {
                $query->where(function ($q) {
                    $q->whereHas('perolehanAset', function ($t) {
                        $t->where('tanggal_masuk', '<=', $this->endDate);
                    })->where(function ($q2) {
                        $q2->whereDoesntHave('disposal')
                            ->orWhereHas('disposal', function ($d) {
                                $d->where('tanggal_keluar', '>', $this->endDate);
                            });
                    });
                });
            }
        } else {
            // Aset aktif = tidak punya tanggal keluar (belum dilepas)
            $query->whereDoesntHave('disposal');

            // Filter tanggal masuk HANYA jika memang ada date range yang diminta
            // dan tidak mengeluarkan aset tanpa transaksi
            if ($this->startDate && $this->endDate) {
                $query->where(function ($qw) {
                    $qw->whereHas('perolehanAset', function ($q) {
                        $q->whereBetween('tanggal_masuk', [$this->startDate, $this->endDate]);
                    })->orWhereDoesntHave('perolehanAset');
                });
            }
        }

        // Kalau laporan kondisi aset, urutkan berdasarkan kondisi
        if ($this->jenisLaporan === 'kondisi_aset') {
            $query->orderBy('kondisi', 'asc');
        }

        $assets = $query->get();

        if ($this->jenisLaporan === 'penyusutan_aset' && $this->startDate && $this->endDate) {
            foreach ($assets as $asset) {
                $start = \Carbon\Carbon::parse($this->startDate)->startOfDay();
                $end = \Carbon\Carbon::parse($this->endDate)->endOfDay();
                $tglMasuk = $asset->perolehanAset && $asset->perolehanAset->tanggal_masuk ? \Carbon\Carbon::parse($asset->perolehanAset->tanggal_masuk)->startOfDay() : clone $start;

                $depStart = $start->greaterThan($tglMasuk) ? $start : $tglMasuk;
                $depEnd = clone $end;
                if ($asset->disposal && $asset->disposal->tanggal_keluar) {
                    $tglKeluar = \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->endOfDay();
                    if ($end->greaterThan($tglKeluar)) {
                        $depEnd = $tglKeluar;
                    }
                }

                $monthsInPeriod = 0;
                if ($depEnd->greaterThanOrEqualTo($depStart)) {
                    $monthsInPeriod = $depStart->diffInMonths($depEnd);
                }

                $totalMonths = 0;
                $accumEnd = clone $end;
                if ($asset->disposal && $asset->disposal->tanggal_keluar) {
                    $tglK = \Carbon\Carbon::parse($asset->disposal->tanggal_keluar)->endOfDay();
                    if ($end->greaterThan($tglK))
                        $accumEnd = $tglK;
                }
                if ($accumEnd->greaterThanOrEqualTo($tglMasuk)) {
                    $totalMonths = $tglMasuk->diffInMonths($accumEnd);
                }

                $maxMonths = $asset->umur_ekonomis_tahun * 12;
                $penyusutanPerBulan = $maxMonths > 0 ? ($asset->harga_perolehan / $maxMonths) : 0;

                $cappedTotalMonths = min($totalMonths, $maxMonths);
                $akumulasi = $cappedTotalMonths * $penyusutanPerBulan;

                $monthsBeforeStart = 0;
                if ($start->greaterThan($tglMasuk)) {
                    $monthsBeforeStart = $tglMasuk->diffInMonths($start);
                }

                $remainingDepreciableMonthsInPeriod = max(0, $maxMonths - $monthsBeforeStart);
                $actualMonthsInPeriod = min($monthsInPeriod, $remainingDepreciableMonthsInPeriod);

                $penyusutanPeriode = $actualMonthsInPeriod * $penyusutanPerBulan;

                $asset->calculated_penyusutan_periode = $penyusutanPeriode;
                $asset->calculated_akumulasi = $akumulasi;
                $asset->calculated_nilai_buku = max(0, $asset->harga_perolehan - $akumulasi);
            }
        }

        return $assets;
    }

    public function getTotals()
    {
        $data = $this->getAssets();

        $totalPenyusutan = 0;
        $totalNilaiBuku = 0;
        $totalHargaPerolehan = 0;
        $totalHargaJual = 0;

        // Khusus laporan penyusutan_aset, kita sediakan total ekstra untuk periode ini
        $totalPenyusutanPeriode = 0;

        foreach ($data as $asset) {
            $totalHargaPerolehan += $asset->harga_perolehan;

            if ($this->jenisLaporan === 'penyusutan_aset' && isset($asset->calculated_akumulasi)) {
                $totalPenyusutan += $asset->calculated_akumulasi;
                $totalNilaiBuku += $asset->calculated_nilai_buku;
                $totalPenyusutanPeriode += $asset->calculated_penyusutan_periode;
            } else {
                $totalPenyusutan += $asset->akumulasi_penyusutan;
                $totalNilaiBuku += $asset->nilai_buku;
            }

            if ($asset->disposal && $asset->disposal->harga_jual) {
                $totalHargaJual += $asset->disposal->harga_jual;
            }
        }

        return [
            'total_harga_perolehan' => $totalHargaPerolehan,
            'total_akumulasi_penyusutan' => $totalPenyusutan,
            'total_nilai_buku' => $totalNilaiBuku,
            'total_harga_jual' => $totalHargaJual,
            'total_penyusutan_periode' => $totalPenyusutanPeriode
        ];
    }

    public function collection()
    {
        $assets = $this->getAssets();
        $totals = $this->getTotals();

        // Sisipkan baris TOTAL di bagian paling bawah keranjang data
        $assets->push((object) [
            'is_total_row' => true,
            'total_harga_perolehan' => $totals['total_harga_perolehan'],
            'total_akumulasi_penyusutan' => $totals['total_akumulasi_penyusutan'],
            'total_nilai_buku' => $totals['total_nilai_buku'],
            'total_harga_jual' => $totals['total_harga_jual'],
            'total_penyusutan_periode' => $totals['total_penyusutan_periode']
        ]);

        return $assets;
    }

    public function map($asset): array
    {
        if ($this->jenisLaporan === 'penyusutan_aset') {
            if (isset($asset->is_total_row)) {
                return [
                    '',
                    '',
                    '',
                    '',
                    '',
                    'TOTAL',
                    $asset->total_harga_perolehan,
                    '',
                    $asset->total_penyusutan_periode,
                    $asset->total_akumulasi_penyusutan,
                    $asset->total_nilai_buku
                ];
            }
            return [
                $asset->nama_aset,
                $asset->merek_aset,
                $asset->category->nama_kategori ?? '-',
                $asset->location->nama_ruangan ?? '-',
                $asset->kondisi,
                $asset->status ?? 'Aktif',
                $asset->harga_perolehan,
                $asset->umur_ekonomis_tahun,
                $asset->calculated_penyusutan_periode ?? 0,
                $asset->calculated_akumulasi ?? 0,
                $asset->calculated_nilai_buku ?? 0
            ];
        }

        if (isset($asset->is_total_row)) {
            return [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'TOTAL',
                $asset->total_harga_perolehan,
                '',
                $asset->total_akumulasi_penyusutan,
                '',
                $asset->total_nilai_buku,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $asset->total_harga_jual,
                '',
                '',
                '',
                ''
            ];
        }

        return [
            $asset->nama_aset,
            $asset->merek_aset,
            $asset->category->nama_kategori ?? '-',
            $asset->location->nama_ruangan ?? '-',
            $asset->kondisi,
            $asset->status ?? 'Aktif',
            $asset->sumber_dana,
            $asset->unit,
            $asset->vendor_aset ?? '-',
            $asset->harga_perolehan,
            $asset->penyusutan_per_tahun,
            $asset->akumulasi_penyusutan,
            $asset->umur_ekonomis_tahun,
            $asset->nilai_buku,

            // Data Transaksi Pembelian/Penerimaan Aset
            $asset->perolehanAset->tanggal_masuk ?? '-',
            $asset->perolehanAset->jenis_transaksi_masuk ?? '-',
            ($asset->perolehanAset && $asset->perolehanAset->is_pihak_kedua_karyawan_masuk) ? 'Ya' : 'Tidak',
            $asset->perolehanAset->nik_pihak_kedua_masuk ?? '-',
            $asset->perolehanAset->nama_pihak_kedua_masuk ?? '-',
            $asset->perolehanAset->alamat_pihak_kedua_masuk ?? '-',

            // Informasi Pelepasan jika ada
            $asset->disposal->tanggal_keluar ?? '-',
            $asset->disposal->jenis_transaksi_keluar ?? '-',
            $asset->disposal->harga_jual ?? '-',
            ($asset->disposal && $asset->disposal->is_pihak_kedua) ? 'Ya' : 'Tidak',
            $asset->disposal->nik_pihak_kedua_keluar ?? '-',
            $asset->disposal->nama_pihak_kedua_keluar ?? '-',
            $asset->disposal->alamat_pihak_kedua_keluar ?? '-',
        ];
    }

    public function headings(): array
    {
        if ($this->jenisLaporan === 'penyusutan_aset') {
            return [
                'Nama Aset',
                'Merk',
                'Kategori',
                'Ruangan Aset',
                'Kondisi',
                'Status',
                'Harga Perolehan',
                'Umur Ekonomis (Thn)',
                'Penyusutan Periode Ini',
                'Akumulasi Penyusutan (s.d Akhir Periode)',
                'Nilai Buku Sisa'
            ];
        }

        return [
            'Nama Aset',
            'Merk',
            'Kategori',
            'Ruangan Aset',
            'Kondisi',
            'Status',
            'Sumber Dana',
            'Unit Penempatan',
            'Vendor/Toko',
            'Harga Perolehan',
            'Penyusutan Per Tahun',
            'Akumulasi Penyusutan',
            'Umur Ekonomis (Thn)',
            'Nilai Buku Saat Ini',

            'Tanggal Masuk',
            'Jenis Pembelian/Hibah',
            'Apakah pihak kedua?', // Sesuai permintaan user
            'NIK Pihak Ke-2 Masuk',
            'Nama Pihak Ke-2 Masuk',
            'Alamat', // Sesuai permintaan user

            'Tanggal Keluar/Pelepasan',
            'Jenis Pelepasan',
            'Harga Jual',
            'Apakah pihak kedua?',
            'NIK Pihak Ke-2 Keluar',
            'Pihak Penerima/Pembeli',
            'Alamat',
        ];
    }

    public function title(): string
    {
        if ($this->jenisLaporan === 'penyusutan_aset')
            return 'Penyusutan Aset';
        if ($this->jenisLaporan === 'total_aset')
            return 'Total Aset';
        return $this->isDisposed ? 'Aset Dilepas' : 'Aset Aktif';
    }
}
