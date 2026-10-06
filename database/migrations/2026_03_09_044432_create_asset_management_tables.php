<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Assets
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ruangan_id')->nullable(); // nullable/integer biasa untuk menghindari error foreign key jika tabel ruangans blm ada
            $table->string('nama_aset', 100);
            $table->string('merek_aset', 100);
            $table->string('vendor_aset', 50)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('sumber_dana', ['Yayasan', 'BOS']);
            $table->string('unit', 50); // Contoh: 'SDIT 2'
            $table->string('kategori', 50); // Contoh: 'Elektronik', 'Inventaris Kelas'
            $table->integer('umur_ekonomis_tahun')->default(0);
            // Menggunakan decimal / bigInteger karena harga (Rupiah) bisa sangat besar
            $table->bigInteger('harga_perolehan')->default(0); 
            $table->timestamps();
        });

        // 2. Tabel Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->enum('jenis_transaksi_masuk', ['Pembelian', 'Hibah']);
            $table->string('bukti_transaksi', 255)->nullable(); // Path file nota/kwitansi
            $table->string('nik_pihak_kedua', 16)->nullable(); // Bisa null jika tidak ada
            $table->text('alamat_pihak_kedua')->nullable();
            $table->date('tanggal_keluar')->nullable(); // Saat aset dijual/dihapus
            $table->bigInteger('harga_jual')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Maintenance Logs
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->date('tanggal_servis');
            $table->text('deskripsi_kerusakan');
            $table->bigInteger('biaya_perbaikan')->default(0);
            $table->date('next_service_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('assets');
    }
};
