<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\PerolehanAset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportAssetsCommand extends Command
{
    protected $signature = 'import:assets';
    protected $description = 'Import assets from CSV file';

    public function handle()
    {
        $csvFile = base_path('2. Manajemen  Aset - SDIT 2.csv'); // Handling the double space
        if (!file_exists($csvFile)) {
            $csvFile = base_path('2. Manajemen Aset - SDIT 2.csv');
        }
        if (!file_exists($csvFile)) {
            $this->error('CSV file not found');
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // read header

        DB::beginTransaction();
        try {
            $count = 0;
            while (($row = fgetcsv($file)) !== false) {
                // skip if row doesn't have enough columns or name is empty
                if (empty($row[0]))
                    continue;

                $namaAset = $row[0] ?? '';
                $merekAset = $row[1] ?? '';
                $vendorAset = $row[2] ?? '';
                $umurTahun = (int) ($row[5] ?? 0);
                $kondisi = preg_replace('/[^a-zA-Z\s]/', '', $row[6] ?? 'Baik');
                if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat']))
                    $kondisi = 'Baik';

                $sumberDana = $row[7] ?? 'Yayasan';
                if (stripos($sumberDana, 'BOS') !== false)
                    $sumberDana = 'BOS';
                else
                    $sumberDana = 'Yayasan';

                $ruanganName = $row[8] ?? '-';
                $unit = $row[9] ?? 'SDIT 2 Dar el-Iman';
                $kategoriName = $row[10] ?? '-';

                try {
                    $tanggalMasuk = !empty($row[12]) ? Carbon::parse($row[12])->format('Y-m-d') : Carbon::now()->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalMasuk = Carbon::now()->format('Y-m-d');
                }

                $jenisTransaksi = $row[13] ?? 'Pembelian';
                if (stripos($jenisTransaksi, 'Hibah') !== false)
                    $jenisTransaksi = 'Hibah masuk';
                else
                    $jenisTransaksi = 'Pembelian';

                $bukti = $row[14] ?? '-';
                $hargaPerolehan = (int) str_replace(['Rp', '.', ',', ' '], '', $row[15] ?? 0);
                $nik = $row[16] ?? '-';
                $isKaryawanStr = strtolower(trim($row[17] ?? 'tidak'));
                $isKaryawan = ($isKaryawanStr === 'ya') ? 1 : 0;
                $namaPihak = $row[18] ?? '-';
                $alamatPihak = $row[19] ?? '-';

                $category = Category::firstOrCreate(['nama_kategori' => $kategoriName]);
                $location = Location::firstOrCreate(['nama_ruangan' => $ruanganName]);

                $asset = Asset::create([
                    'nama_aset' => $namaAset,
                    'merek_aset' => $merekAset,
                    'vendor_aset' => $vendorAset,
                    'kondisi' => $kondisi,
                    'sumber_dana' => $sumberDana,
                    'unit' => $unit,
                    'category_id' => $category->id,
                    'location_id' => $location->id,
                    'umur_ekonomis_tahun' => $umurTahun,
                    'harga_perolehan' => $hargaPerolehan,
                    'is_layak_pakai' => 1,
                    // UUID logic for Asset Code
                    'kode_aset' => 'AST-' . time() . '-' . rand(1000, 9999) . '-' . $count,
                ]);

                PerolehanAset::create([
                    'asset_id' => $asset->id,
                    'tanggal_masuk' => $tanggalMasuk,
                    'jenis_transaksi_masuk' => $jenisTransaksi,
                    'bukti_transaksi_masuk' => '-', // default to standard placeholder for old uploads
                    'is_pihak_kedua_karyawan_masuk' => $isKaryawan,
                    'nik_pihak_kedua_masuk' => $nik,
                    'nama_pihak_kedua_masuk' => $namaPihak,
                    'alamat_pihak_kedua_masuk' => $alamatPihak,
                ]);

                $count++;
            }
            DB::commit();
            $this->info("Successfully imported $count assets!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage() . " at line " . $e->getLine());
        }
        fclose($file);
    }
}
