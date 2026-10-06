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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('kode_aset', 50)->nullable()->unique()->after('id');
            $table->boolean('is_rutin_servis')->default(false)->after('umur_ekonomis_tahun');
            $table->integer('interval_servis')->nullable()->after('is_rutin_servis');
            $table->date('tanggal_servis_terakhir')->nullable()->after('interval_servis');
            $table->date('tanggal_servis_berikutnya')->nullable()->after('tanggal_servis_terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'kode_aset',
                'is_rutin_servis',
                'interval_servis',
                'tanggal_servis_terakhir',
                'tanggal_servis_berikutnya'
            ]);
        });
    }
};
