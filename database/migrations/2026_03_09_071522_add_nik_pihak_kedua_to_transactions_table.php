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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'nik_pihak_kedua_masuk')) {
                $table->string('nik_pihak_kedua_masuk', 16)->nullable()->after('is_pihak_kedua_karyawan_masuk');
            }
            if (!Schema::hasColumn('transactions', 'nik_pihak_kedua_keluar')) {
                $table->string('nik_pihak_kedua_keluar', 16)->nullable()->after('is_pihak_kedua_karyawan_keluar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'nik_pihak_kedua_masuk')) {
                $table->dropColumn('nik_pihak_kedua_masuk');
            }
            if (Schema::hasColumn('transactions', 'nik_pihak_kedua_keluar')) {
                $table->dropColumn('nik_pihak_kedua_keluar');
            }
        });
    }
};
