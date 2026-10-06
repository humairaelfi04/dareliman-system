<?php

namespace App\Exports;

use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Carbon;

class MaintenanceLogsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    // MENERIMA PESANAN: Menyimpan pilihan range tanggal dari pengguna saat tombol Export diklik
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // MENGAMBIL OBJEK: Mengambil daftar riwayat servis dari database. Jika ada filter tanggal, datanya disaring dulu.
    public function getAssets()
    {
        $query = MaintenanceLog::with('asset')->orderBy('tanggal_servis', 'desc');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_servis', [$this->startDate, $this->endDate]);
        }

        return $query->get();
    }

    // MENGHITUNG BIAYA: Menjumlahkan seluruh uang biaya perbaikan/servis
    public function getTotals()
    {
        $data = $this->getAssets();
        $totalBiaya = 0;
        foreach ($data as $log) {
            $totalBiaya += $log->biaya_perbaikan;
        }
        return ['total_biaya' => $totalBiaya];
    }

    // MENGGABUNGKAN DATA: Menyatukan daftar riwayat dengan menempelkan baris SUM/Total Biaya di urutan paling bawah
    public function collection()
    {
        $logs = $this->getAssets();

        $totalBiaya = 0;
        foreach ($logs as $log) {
            $totalBiaya += $log->biaya_perbaikan;
        }

        $logs->push((object) [
            'is_total_row' => true,
            'total_biaya' => $totalBiaya
        ]);

        return $logs;
    }

    // MENGISI KOLOM EXCEL: Memetakan setiap data riwayat servis ke kolom-kolom Excel masing-masing secara berurutan
    public function map($log): array
    {
        if (isset($log->is_total_row)) {
            return [
                '',
                '',
                '',
                'TOTAL BIAYA SERVIS',
                $log->total_biaya,
                ''
            ];
        }

        $nextDate = $log->next_service_date;

        return [
            $log->asset->kode_aset ?? '-',
            $log->asset->nama_aset ?? '-',
            $log->tanggal_servis,
            $log->deskripsi_kerusakan,
            $log->biaya_perbaikan,
            $nextDate ? Carbon::parse($nextDate)->format('d/m/Y') : '-',
        ];
    }

    // MENULIS JUDUL ATAS: Memberikan teks judul kolom pada baris pertama di dalam Excel
    public function headings(): array
    {
        return [
            'Kode Aset',
            'Nama Aset',
            'Tanggal Servis',
            'Deskripsi Kerusakan / Servis',
            'Biaya Perbaikan/Servis (Rp)',
            'Jadwal Berikutnya',
        ];
    }

    // MEMBERI NAMA KERTAS KERJA: Memberikan nama untuk Tab/Sheet di bagian bawah aplikasi Microsoft Excel Anda
    public function title(): string
    {
        return 'Riwayat Maintenis';
    }
}
