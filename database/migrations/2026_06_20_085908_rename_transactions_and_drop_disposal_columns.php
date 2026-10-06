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
        Schema::rename('transactions', 'perolehan_aset');

        Schema::table('perolehan_aset', function (Blueprint $table) {
            $table->renameColumn('bukti_transaksi', 'bukti_transaksi_masuk');
            $table->dropColumn([
                'tanggal_keluar',
                'jenis_transaksi_keluar',
                'harga_jual',
                'bukti_transaksi_keluar',
                'is_pihak_kedua_karyawan_keluar',
                'nik_pihak_kedua_keluar',
                'nama_pihak_kedua_keluar',
                'alamat_pihak_kedua_keluar',
                'nik_pihak_kedua',
                'alamat_pihak_kedua',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perolehan_aset', function (Blueprint $table) {
            $table->date('tanggal_keluar')->nullable();
            $table->string('jenis_transaksi_keluar', 50)->nullable();
            $table->bigInteger('harga_jual')->nullable();
            $table->string('bukti_transaksi_keluar', 255)->nullable();
            $table->boolean('is_pihak_kedua_karyawan_keluar')->default(false);
            $table->string('nik_pihak_kedua_keluar', 16)->nullable();
            $table->string('nama_pihak_kedua_keluar', 50)->nullable();
            $table->text('alamat_pihak_kedua_keluar')->nullable();
            $table->string('nik_pihak_kedua', 16)->nullable();
            $table->text('alamat_pihak_kedua')->nullable();
            $table->renameColumn('bukti_transaksi_masuk', 'bukti_transaksi');
        });

        Schema::rename('perolehan_aset', 'transactions');
    }
};
