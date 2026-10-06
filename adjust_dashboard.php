<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use Carbon\Carbon;

try {
    // 1. Bersihkan semua expire notice dengan set is_layak_pakai = true
    Asset::query()->update(['is_layak_pakai' => true]);

    // 2. Buat tepat 5 aset muncul di "Status Umur Pemakaian" (expired & belum layak pakai)
    $expiredAssets = Asset::whereDoesntHave('disposal')->take(5)->get();
    foreach ($expiredAssets as $asset) {
        // Dengan umur_ekonomis 0, dia akan selalu dianggap langsung expired sejak tanggal masuk
        $asset->is_layak_pakai = false;
        $asset->umur_ekonomis_tahun = 0;
        $asset->save();
    }
    echo "Berhasil mengatur 5 aset expired.\n";

    // 3. Buat tepat 3 aset muncul di "Pengingat Jadwal Perawatan" (rutin servis)
    $maintenanceAssets = Asset::whereDoesntHave('disposal')->skip(5)->take(3)->get();
    foreach ($maintenanceAssets as $asset) {
        $asset->is_rutin_servis = true;
        $asset->interval_servis = 1;
        $asset->tanggal_servis_terakhir = Carbon::now()->subDays(20);
        $asset->tanggal_servis_berikutnya = Carbon::now()->addDays(10);
        $asset->save();
    }
    echo "Berhasil mengatur 3 aset jadwal perawatan.\n";

    echo "Selesai mengatur dashboard data.\n";
} catch (\Exception $e) {
    file_put_contents('dash_err.txt', $e->getMessage());
    echo "Error occurred, written to dash_err.txt\n";
}
