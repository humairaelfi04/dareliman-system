<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use App\Models\PerolehanAset;
use Carbon\Carbon;

$file = fopen('2. Manajemen Aset - SDIT 2.csv', 'r');
if (!$file) {
    echo "Fail to open file\n";
    exit;
}

// Read header
fgetcsv($file);

$count = 0;
while (($row = fgetcsv($file)) !== false) {
    try {
        if (empty($row[0]))
            continue;

        $namaAset = trim($row[0]);
        $merek = trim($row[1]);
        $vendor = trim($row[2]);
        $umurTahun = (int) trim($row[5]);
        $umurBulan = $umurTahun * 12 + (int) trim($row[4]); // total months
        $kondisi = trim($row[6]);
        $sumberDana = trim($row[7]);
        $ruanganName = trim($row[8]);
        $unit = trim($row[9]);
        $kategoriName = trim($row[10]);

        // Transaction IN
        $tglMasuk = trim($row[12]);
        $jenisMasuk = trim($row[13]);
        $buktiMasuk = trim($row[14]);
        $harga = (float) trim($row[15]);

        // Get or create Category
        $category = Category::firstOrCreate(['nama_kategori' => $kategoriName]);

        // Get or create Location
        $location = Location::firstOrCreate(['nama_ruangan' => $ruanganName]);

        // Generate code
        $kodeAset = 'AST-' . date('Ym', strtotime($tglMasuk)) . '-' . rand(1000, 9999) . '-' . $count;

        // Create Asset
        $asset = Asset::create([
            'kode_aset' => $kodeAset,
            'nama_aset' => $namaAset,
            'merek_aset' => $merek !== '-' ? $merek : null,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'kondisi' => strtolower($kondisi),
            'unit' => $unit,
            'vendor_aset' => $vendor,
            'sumber_dana' => rtrim(strtolower($sumberDana), ' '),
            'harga_perolehan' => $harga,
            'umur_ekonomis_tahun' => $umurTahun,
        ]);

        // Fix enum sumber dana
        if (!in_array($asset->sumber_dana, ['bos', 'koperasi', 'yayasan'])) {
            $asset->sumber_dana = 'yayasan';
            $asset->save();
        }

        if (!in_array($asset->kondisi, ['baik', 'rusak ringan', 'rusak berat'])) {
            $asset->kondisi = 'baik';
            $asset->save();
        }

        // Create Perolehan Aset
        PerolehanAset::create([
            'asset_id' => $asset->id,
            'tanggal_masuk' => $tglMasuk,
            'jenis_transaksi_masuk' => strtolower($jenisMasuk),
            'bukti_transaksi_masuk' => $buktiMasuk !== '-' ? $buktiMasuk : null,
            'is_pihak_kedua_karyawan_masuk' => false,
            'nik_pihak_kedua_masuk' => null,
        ]);

        $count++;
    } catch (\Exception $e) {
        file_put_contents('err.txt', "ERROR at row " . ($count + 2) . ": " . $e->getMessage());
        break;
    }
}
fclose($file);
echo "Successfully imported $count assets.\n";

// MOCKING DATA
use App\Models\DisposalRequest;
use App\Models\MaintenanceLog;
use App\Models\User;

$admin = User::first();
if (!$admin) {
    echo "No user found to mock data.\n";
    exit;
}

// 1. Aset sedang pengajuan pelepasan (Pending Disposal)
$assetPending = Asset::where('kondisi', 'rusak berat')->first();
if ($assetPending) {
    DisposalRequest::create([
        'asset_id' => $assetPending->id,
        'user_id' => $admin->id,
        'alasan' => 'Barang sudah hancur total',
        'status' => 'pending'
    ]);
    echo "Mocked 1 Pending Disposal (Asset: $assetPending->nama_aset)\n";
}

// 2. Aset yang sudah dilepas (Approved Disposal)
$assetDisposed = Asset::where('kondisi', 'rusak berat')->skip(1)->first();
if ($assetDisposed) {
    DisposalRequest::create([
        'asset_id' => $assetDisposed->id,
        'user_id' => $admin->id,
        'alasan' => 'Mesin terbakar hangus',
        'status' => 'approved',
        'approved_by' => $admin->id,
        'tanggal_persetujuan' => now(),
        'metode_pelepasan' => 'dibuang',
        'keterangan_pelepasan' => 'Dibuang ke tong sampah'
    ]);
    echo "Mocked 1 Approved Disposal (Asset: $assetDisposed->nama_aset)\n";
}

// 3. Konfirmasi selesai servis (Completed Maintenance)
$assetMaintenance = Asset::where('kondisi', 'rusak ringan')->first();
if ($assetMaintenance) {
    MaintenanceLog::create([
        'asset_id' => $assetMaintenance->id,
        'tanggal_servis' => now()->subDays(5),
        'masalah' => 'Kipas tidak menyala',
        'tindakan' => 'Diganti dinamo baru',
        'biaya' => 150000,
        'status' => 'selesai',
        'teknisi' => 'Pak Joko Teknisi'
    ]);
    // Asumsi kondisi jadi baik lagi setelah diservis
    $assetMaintenance->kondisi = 'baik';
    $assetMaintenance->save();
    echo "Mocked 1 Completed Maintenance (Asset: $assetMaintenance->nama_aset)\n";
}

// 4. Verifikasi layak pakai (Expired but usable)
// Asset where age is expired but still usable.
// Just mock a really old perolehan date
$assetUsable = Asset::skip(5)->first();
if ($assetUsable) {
    $perolehan = $assetUsable->perolehan();
    if ($perolehan) {
        // Set purchase date 10 years ago so it's expired
        $perolehan->update(['tanggal_perolehan' => now()->subYears(10)]);
        $assetUsable->is_layak_pakai = true;
        $assetUsable->kondisi = 'baik';
        $assetUsable->save();
        echo "Mocked 1 Verified Usable Expired Asset (Asset: $assetUsable->nama_aset)\n";
    }
}

echo "All tasks completed.\n";
