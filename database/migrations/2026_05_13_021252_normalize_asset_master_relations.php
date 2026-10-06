<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom FK (nullable dulu agar tidak error saat ada data)
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('kategori');
            $table->unsignedBigInteger('location_id')->nullable()->after('ruangan_aset');
        });

        // 2. Migrasi Data String ke FK
        $assets = Asset::all();
        foreach ($assets as $asset) {
            // Migrasi Kategori
            if (!empty($asset->kategori)) {
                $category = Category::firstOrCreate(['nama_kategori' => $asset->kategori]);
                $asset->category_id = $category->id;
            }
            // Migrasi Ruangan (Lokasi)
            if (!empty($asset->ruangan_aset)) {
                $location = Location::firstOrCreate(['nama_ruangan' => $asset->ruangan_aset]);
                $asset->location_id = $location->id;
            }
            // Simpan tanpa update timestamps untuk menjaga keaslian data (opsional, tapi disarankan pakai DB facade untuk keamanan)
            if ($asset->isDirty()) {
                DB::table('assets')->where('id', $asset->id)->update([
                    'category_id' => $asset->category_id,
                    'location_id' => $asset->location_id
                ]);
            }
        }

        // 3. Hapus kolom string lama & set Foreign Key Constraints
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'ruangan_aset']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop FK constraints dan kolom
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['location_id']);
            $table->string('kategori', 50)->nullable()->after('category_id');
            $table->string('ruangan_aset', 50)->nullable()->after('location_id');
        });

        // 2. Kembalikan data FK ke String
        $assets = DB::table('assets')
                    ->leftJoin('categories', 'assets.category_id', '=', 'categories.id')
                    ->leftJoin('locations', 'assets.location_id', '=', 'locations.id')
                    ->select('assets.id', 'categories.nama_kategori', 'locations.nama_ruangan')
                    ->get();
                    
        foreach ($assets as $asset) {
            DB::table('assets')->where('id', $asset->id)->update([
                'kategori' => $asset->nama_kategori,
                'ruangan_aset' => $asset->nama_ruangan
            ]);
        }

        // 3. Hapus kolom FK
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'location_id']);
        });
    }
};
