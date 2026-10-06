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
        Schema::table('asset_histories', function (Blueprint $table) {
            $table->string('jenis_perubahan', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_histories', function (Blueprint $table) {
            $table->enum('jenis_perubahan', ['Mutasi Ruangan', 'Perubahan Kondisi'])->change();
        });
    }
};
