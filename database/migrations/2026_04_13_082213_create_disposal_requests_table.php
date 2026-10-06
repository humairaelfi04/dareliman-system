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
        Schema::create('disposal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->date('tanggal_keluar');
            $table->string('jenis_transaksi_keluar', 50);
            $table->bigInteger('harga_jual')->nullable();
            $table->string('bukti_transaksi_keluar', 255)->nullable();
            $table->boolean('is_pihak_kedua_karyawan_keluar')->default(false);
            $table->string('nik_pihak_kedua_keluar', 16)->nullable();
            $table->string('nama_pihak_kedua_keluar', 50)->nullable();
            $table->text('alamat_pihak_kedua_keluar')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_requests');
    }
};
