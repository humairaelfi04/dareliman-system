<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_maintenance_details', function (Blueprint $table) {
            for ($i = 1; $i <= 6; $i++) {
                $table->string("kondisi_b{$i}", 50)->nullable();
                $table->string("status_temuan_b{$i}", 50)->nullable();
                $table->string("catatan_temuan_b{$i}", 100)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_maintenance_details', function (Blueprint $table) {
            for ($i = 1; $i <= 6; $i++) {
                $table->dropColumn("kondisi_b{$i}");
                $table->dropColumn("status_temuan_b{$i}");
                $table->dropColumn("catatan_temuan_b{$i}");
            }
        });
    }
};
