<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('disposal_requests', function (Blueprint $table) {
            $table->text('alasan_pengajuan')->nullable()->after('asset_id');
            $table->string('foto_kondisi', 255)->nullable()->after('alasan_pengajuan');
            $table->date('tanggal_keluar')->nullable()->change();
            $table->string('jenis_transaksi_keluar', 50)->nullable()->change();
        });

        // Add 'completed' to ENUM
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE disposal_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposal_requests', function (Blueprint $table) {
            $table->dropColumn(['alasan_pengajuan', 'foto_kondisi']);
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE disposal_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
