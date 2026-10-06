<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Location;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan tabel locations sudah ada, baru kita inject
        if (Schema::hasTable('locations')) {
            Location::firstOrCreate(
                ['nama_ruangan' => 'Gudang Sementara'],
                ['kategori_ruangan' => 'Gudang', 'penanggung_jawab' => 'Admin Sarpras']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('locations')) {
            Location::where('nama_ruangan', 'Gudang Sementara')->delete();
        }
    }
};
