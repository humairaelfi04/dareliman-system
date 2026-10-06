<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use App\Models\PerolehanAset;
use App\Models\DisposalRequest;
use App\Models\MaintenanceLog;
use App\Models\User;

$admin = User::first();

// 1. Aset sedang pengajuan pelepasan (Pending Disposal)
$assetPending = Asset::where('kondisi', 'rusak berat')->first();
if ($assetPending) {
    DisposalRequest::create([
        'asset_id' => $assetPending->id,
        'user_id' => $admin->id,
        'alasan_pengajuan' => 'Barang sudah hancur total',
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
        'alasan_pengajuan' => 'Besi berkarat, tidak dapat diperbaiki',
        'status' => 'approved',
        'tanggal_keluar' => now(),
        'jenis_transaksi_keluar' => 'dibuang',
    ]);
    echo "Mocked 1 Approved Disposal (Asset: $assetDisposed->nama_aset)\n";
}

// 3. Konfirmasi selesai servis (Completed Maintenance)
$assetMaintenance = Asset::where('kondisi', 'rusak ringan')->first();
if ($assetMaintenance) {
    MaintenanceLog::create([
        'asset_id' => $assetMaintenance->id,
        'tanggal_servis' => now()->subDays(5),
        'deskripsi_kerusakan' => 'Kipas mati total, mesin sudah diganti dengan sparepart baru dan diuji coba',
        'biaya_perbaikan' => 150000,
    ]);
    $assetMaintenance->kondisi = 'baik';
    $assetMaintenance->save();
    echo "Mocked 1 Completed Maintenance (Asset: $assetMaintenance->nama_aset)\n";
}

// 4. Verifikasi layak pakai (Expired but usable)
$assetUsable = Asset::skip(5)->first();
if ($assetUsable) {
    $perolehan = $assetUsable->perolehan;
    if ($perolehan) {
        $perolehan->update(['tanggal_masuk' => now()->subYears(10)]);
        $assetUsable->is_layak_pakai = true;
        $assetUsable->kondisi = 'baik';
        $assetUsable->save();
        echo "Mocked 1 Verified Usable Expired Asset (Asset: $assetUsable->nama_aset)\n";
    }
}
