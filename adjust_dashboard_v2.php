<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use Carbon\Carbon;

try {
    // 1. OLAH DATA KADALUARSA (Expired Assets)
    // By default, newly imported assets have is_layak_pakai = false.
    // Dashboard checks sisaHari <= 90. Let's find all naturally expired assets.
    $assets = Asset::with('perolehanAset')->whereDoesntHave('disposal')->get();

    $expiredIds = [];
    foreach ($assets as $asset) {
        if ($asset->perolehanAset && $asset->perolehanAset->tanggal_masuk) {
            $tanggalBeli = Carbon::parse($asset->perolehanAset->tanggal_masuk);
            $tanggalBatas = $tanggalBeli->copy()->addYears($asset->umur_ekonomis_tahun);
            $sisaHari = Carbon::now()->diffInDays($tanggalBatas, false);

            if ($sisaHari <= 90 && !$asset->is_layak_pakai) {
                $expiredIds[] = $asset->id;
            }
        }
    }

    echo "Found " . count($expiredIds) . " naturally expired assets.\n";

    if (count($expiredIds) > 5) {
        // Keep the first 5, mark the rest as layak_pakai so they disappear from the alert
        $idsToVerify = array_slice($expiredIds, 5);
        Asset::whereIn('id', $idsToVerify)->update(['is_layak_pakai' => true]);
        echo "Set is_layak_pakai = true for " . count($idsToVerify) . " assets.\n";
    }

    // 2. OLAH DATA JADWAL PERAWATAN (Maintenance Assets)
    // Carilah aset yang relevan untuk perawatan rutin (misal ada unsur Elektronik, AC, Mesin)
    $relevantAssets = Asset::whereDoesntHave('disposal')
        ->where(function ($q) {
            $q->where('nama_aset', 'LIKE', '%AC%')
                ->orWhere('nama_aset', 'LIKE', '%Printer%')
                ->orWhere('nama_aset', 'LIKE', '%Proyektor%')
                ->orWhere('nama_aset', 'LIKE', '%Komputer%')
                ->orWhere('nama_aset', 'LIKE', '%Kendaraan%')
                ->orWhere('nama_aset', 'LIKE', '%Motor%');
        })
        ->take(3)
        ->get();

    // Jika pencarian dengan kata kunci gagal mendapatkan 3, fallback ambil aset apa saja
    if ($relevantAssets->count() < 3) {
        $needed = 3 - $relevantAssets->count();
        $fallback = Asset::whereDoesntHave('disposal')
            ->whereNotIn('id', $relevantAssets->pluck('id'))
            ->take($needed)->get();
        $relevantAssets = $relevantAssets->concat($fallback);
    }

    foreach ($relevantAssets as $asset) {
        $asset->is_rutin_servis = true;
        $asset->interval_servis = 3; // 3 bulan
        $asset->tanggal_servis_terakhir = Carbon::now()->subDays(85); // 85 hari yang lalu
        $asset->tanggal_servis_berikutnya = Carbon::now()->addDays(5); // jatuh tempo dlm 5 hari
        $asset->save();
        echo "Set maintenance schedule for: " . $asset->nama_aset . "\n";
    }

    echo "Dashboard mockup data adjusted organically.\n";

} catch (\Exception $e) {
    file_put_contents('dash_err.txt', $e->getMessage());
    echo "Error occurred, written to dash_err.txt\n";
}
