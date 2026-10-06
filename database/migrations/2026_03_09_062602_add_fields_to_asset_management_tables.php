<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter tabel assets
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'ruangan_aset')) {
                $table->string('ruangan_aset', 50)->nullable()->after('kategori');
            }
        });
        
        // Agar tidak terjadi 'Data truncated for column', 
        // 1. Ubah sementara kolom enum menjadi VARCHAR
        DB::statement('ALTER TABLE assets MODIFY COLUMN kategori VARCHAR(255)');
        DB::statement('ALTER TABLE assets MODIFY COLUMN sumber_dana VARCHAR(255)');
        
        // 2. Jika ada data lama yang nilainya tidak ada di ENUM baru, 
        // update ke default value, misal 'Lainnya' dan 'Operasional'
        DB::table('assets')->whereNotIn('sumber_dana', ['Yayasan', 'BOS', 'Wakaf', 'Hibah', 'Lainnya'])
                           ->update(['sumber_dana' => 'Lainnya']);
                           
        DB::table('assets')->whereNotIn('kategori', ['Elektronik', 'Kendaraan', 'Gedung dan bangunan', 'Tanah dan bangunan', 'Furniture', 'Property', 'Operasional'])
                           ->update(['kategori' => 'Operasional']);

        // 3. Ubah menjadi ENUM list yang baru
        DB::statement("ALTER TABLE assets MODIFY COLUMN sumber_dana ENUM('Yayasan', 'BOS', 'Wakaf', 'Hibah', 'Lainnya')");
        DB::statement("ALTER TABLE assets MODIFY COLUMN kategori ENUM('Elektronik', 'Kendaraan', 'Gedung dan bangunan', 'Tanah dan bangunan', 'Furniture', 'Property', 'Operasional')");

        // Alter tabel transactions
        Schema::table('transactions', function (Blueprint $table) {
            // Field masuk
            if (!Schema::hasColumn('transactions', 'is_pihak_kedua_karyawan_masuk')) {
                $table->boolean('is_pihak_kedua_karyawan_masuk')->default(false)->after('bukti_transaksi');
                $table->string('nama_pihak_kedua_masuk', 50)->nullable()->after('is_pihak_kedua_karyawan_masuk');
                $table->text('alamat_pihak_kedua_masuk')->nullable()->after('nama_pihak_kedua_masuk');
            }

            // Field keluar
            if (!Schema::hasColumn('transactions', 'jenis_transaksi_keluar')) {
                $table->enum('jenis_transaksi_keluar', ['Hibah keluar', 'Pemindahan keluar', 'Penghapusan', 'Penjualan'])->nullable()->after('tanggal_keluar');
                $table->string('bukti_transaksi_keluar', 255)->nullable()->after('jenis_transaksi_keluar');
                $table->boolean('is_pihak_kedua_karyawan_keluar')->default(false)->after('bukti_transaksi_keluar');
                $table->string('nama_pihak_kedua_keluar', 50)->nullable()->after('is_pihak_kedua_karyawan_keluar');
                $table->text('alamat_pihak_kedua_keluar')->nullable()->after('nama_pihak_kedua_keluar');
            }
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN jenis_transaksi_masuk ENUM('Hibah masuk', 'Pembelian', 'Pemindahan masuk', 'Pinjaman', 'Sewa')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_management_tables', function (Blueprint $table) {
            //
        });
    }
};
