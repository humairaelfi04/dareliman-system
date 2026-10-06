<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_maintenance_cards', function (Blueprint $table) {
            $table->id();
            $table->string('ruangan_name', 50);
            $table->string('penanggung_jawab', 50)->nullable();
            $table->integer('tahun');
            $table->integer('semester'); // 1: Jan-Jun, 2: Jul-Des
            $table->timestamps();
        });

        Schema::create('room_maintenance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_maintenance_card_id')->constrained('room_maintenance_cards')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('kondisi', 50)->nullable(); // Baik/Rusak Ringan/Rusak Berat
            $table->text('catatan_temuan')->nullable();
            $table->boolean('b1')->default(0); // Jan atau Jul
            $table->boolean('b2')->default(0); // Feb atau Ags
            $table->boolean('b3')->default(0); // Mar atau Sep
            $table->boolean('b4')->default(0); // Apr atau Okt
            $table->boolean('b5')->default(0); // Mei atau Nov
            $table->boolean('b6')->default(0); // Jun atau Des
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_maintenance_details');
        Schema::dropIfExists('room_maintenance_cards');
    }
};
