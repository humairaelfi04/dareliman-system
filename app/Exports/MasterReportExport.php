<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MasterReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $sumberDana, $startDate, $endDate, $inclusions;

    // FUNGSI PERTAMA: Menerima Pesanan Filter (tanggal, jenis sumber dana, dan halaman apa saja yang mau dicetak)
    public function __construct($sumberDana, $startDate, $endDate, $inclusions = [])
    {
        $this->sumberDana = $sumberDana;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->inclusions = $inclusions;
    }

    // FUNGSI GABUNG KERTAS KERJA: Menyatukan lembar-lembar laporan terpisah menjadi 1 file buku Excel (Multiple Sheets)
    public function sheets(): array
    {
        $sheets = [];

        if (empty($this->inclusions) || in_array('aset_aktif', $this->inclusions)) {
            $sheets[] = new AssetsExport($this->sumberDana, $this->startDate, $this->endDate, 'daftar_aset', false);
        }

        if (empty($this->inclusions) || in_array('total_aset', $this->inclusions)) {
            $sheets[] = new AssetsExport($this->sumberDana, $this->startDate, $this->endDate, 'total_aset', false);
        }

        if (empty($this->inclusions) || in_array('penyusutan_aset', $this->inclusions)) {
            $sheets[] = new AssetsExport($this->sumberDana, $this->startDate, $this->endDate, 'penyusutan_aset', false);
        }

        if (empty($this->inclusions) || in_array('aset_lepas', $this->inclusions) || in_array('pelepasan_aset', $this->inclusions)) {
            $sheets[] = new AssetsExport($this->sumberDana, $this->startDate, $this->endDate, 'pelepasan_aset', true);
        }

        if (empty($this->inclusions) || in_array('pemeliharaan', $this->inclusions)) {
            $sheets[] = new MaintenanceLogsExport($this->startDate, $this->endDate);
        }

        return $sheets;
    }
}
