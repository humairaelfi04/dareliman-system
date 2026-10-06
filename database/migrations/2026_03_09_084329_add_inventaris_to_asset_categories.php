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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE assets MODIFY COLUMN kategori ENUM('Elektronik', 'Kendaraan', 'Gedung dan bangunan', 'Tanah dan bangunan', 'Furniture', 'Property', 'Operasional', 'Inventaris') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE assets MODIFY COLUMN kategori ENUM('Elektronik', 'Kendaraan', 'Gedung dan bangunan', 'Tanah dan bangunan', 'Furniture', 'Property', 'Operasional') NOT NULL");
    }
};
