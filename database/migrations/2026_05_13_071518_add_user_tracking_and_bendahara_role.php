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
        // 1. Tambah role bendahara
        // Menggunakan raw query karena mengubah ENUM di SQLite/MySQL dengan schema builder kadang bermasalah
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kepala_sekolah', 'bendahara') DEFAULT 'admin'");

        // 2. Tambah user_id di asset_histories
        Schema::table('asset_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('asset_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Tambah user_id (pengaju) dan approved_by (penyetuju) di disposal_requests
        Schema::table('disposal_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('asset_id');
            $table->unsignedBigInteger('approved_by')->nullable()->after('user_id');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposal_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['user_id', 'approved_by']);
        });

        Schema::table('asset_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kepala_sekolah') DEFAULT 'admin'");
    }
};
