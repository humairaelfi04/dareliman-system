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
        // 1. Drop audit_details table (depends on audits)
        Schema::dropIfExists('audit_details');
        
        // 2. Drop audits table
        Schema::dropIfExists('audits');

        // 3. Update room_maintenance_details
        Schema::table('room_maintenance_details', function (Blueprint $table) {
            $table->string('status_temuan', 50)->default('Sesuai')->after('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_maintenance_details', function (Blueprint $table) {
            $table->dropColumn('status_temuan');
        });

        // Recreate audits table
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('location_name', 50);
            $table->date('tanggal_audit');
            $table->string('auditor_name', 100);
            $table->string('status', 50)->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Recreate audit_details table
        Schema::create('audit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('audits')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('status_temuan', 50)->default('Ada');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
};
