<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\DisposalRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard dengan Early Warning System (Bisa diakses Admin & Kepsek)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
});

// Fitur Laporan, Export (Admin, Bendahara & Kepsek)
Route::middleware(['auth', 'role:admin,bendahara,kepala_sekolah,sarpras'])->group(function () {
    Route::get('/assets/export-excel', [AssetController::class, 'exportExcel'])->name('assets.export-excel');
    Route::get('/assets/export-pdf', [AssetController::class, 'exportPdf'])->name('assets.export-pdf');

    // Formulir Laporan Periodik
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/preview', [ReportController::class, 'preview'])->name('preview');
    });
});

// Aset Dilepas & Approval Pelepasan Aset (Admin, Bendahara & Kepsek)
Route::middleware(['auth', 'role:admin,bendahara,kepala_sekolah,sarpras'])->group(function () {
    Route::get('assets/disposed', [AssetController::class, 'disposedIndex'])->name('assets.disposed');
    Route::get('assets/disposed/export-excel', [AssetController::class, 'exportDisposedExcel'])->name('assets.disposed.export-excel');
    Route::get('assets/disposed/export-pdf', [AssetController::class, 'exportDisposedPdf'])->name('assets.disposed.export-pdf');

    Route::get('/disposal-requests', [DisposalRequestController::class, 'index'])->name('disposal-requests.index');
    Route::put('/disposal-requests/{disposalRequest}/approve', [DisposalRequestController::class, 'approve'])->name('disposal-requests.approve');
    Route::put('/disposal-requests/{disposalRequest}/reject', [DisposalRequestController::class, 'reject'])->name('disposal-requests.reject');
    Route::put('/disposal-requests/{disposalRequest}/finalize', [DisposalRequestController::class, 'finalize'])->name('disposal-requests.finalize');
});

// Fitur Manajemen Utama
Route::middleware(['auth'])->group(function () {

    // Manajemen Aset (Admin & Bendahara)
    Route::middleware(['role:admin,bendahara,sarpras'])->prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');

        // CRUD Routes

        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');


        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::put('/{asset}/complete-service', [AssetController::class, 'completeService'])->name('complete-service');
        Route::put('/{asset}/dispose', [AssetController::class, 'dispose'])->name('dispose');
        Route::put('/{asset}/verify-layak', [AssetController::class, 'verifyLayak'])->name('verify-layak');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
    });

    // Master Data (Kategori & Ruangan) - Admin & Bendahara
    Route::middleware(['role:admin,bendahara,sarpras'])->prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('index');

        // Kategori
        Route::post('/categories', [\App\Http\Controllers\MasterDataController::class, 'storeCategory'])->name('categories.store');
        Route::delete('/categories/{category}', [\App\Http\Controllers\MasterDataController::class, 'destroyCategory'])->name('categories.destroy');

        // Ruangan / Lokasi
        Route::post('/locations', [\App\Http\Controllers\MasterDataController::class, 'storeLocation'])->name('locations.store');
        Route::put('/locations/{location}', [\App\Http\Controllers\MasterDataController::class, 'updateLocation'])->name('locations.update');
        Route::delete('/locations/{location}', [\App\Http\Controllers\MasterDataController::class, 'destroyLocation'])->name('locations.destroy');
    });

    // Logs Pemeliharaan - Admin & Bendahara
    Route::middleware(['role:admin,bendahara,sarpras'])->group(function () {
        Route::get('maintenance-logs/export-excel', [\App\Http\Controllers\MaintenanceLogController::class, 'exportExcel'])->name('maintenance-logs.export-excel');
        Route::get('maintenance-logs/export-pdf', [\App\Http\Controllers\MaintenanceLogController::class, 'exportPdf'])->name('maintenance-logs.export-pdf');
        Route::resource('maintenance-logs', \App\Http\Controllers\MaintenanceLogController::class)->only(['index', 'store']);
    });




    // Kartu Pemeliharaan Ruangan (KIR) - Admin & Bendahara
    Route::middleware(['role:admin,bendahara,sarpras'])->prefix('room-maintenances')->name('room-maintenances.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RoomMaintenanceController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\RoomMaintenanceController::class, 'store'])->name('store');
        Route::get('/{roomMaintenance}/edit', [\App\Http\Controllers\RoomMaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{roomMaintenance}', [\App\Http\Controllers\RoomMaintenanceController::class, 'update'])->name('update');

        Route::get('/{roomMaintenance}/export-pdf', [\App\Http\Controllers\RoomMaintenanceController::class, 'exportPdf'])->name('export-pdf');
    });

    // Detail Aset - Admin, Bendahara, Kepsek (ditempatkan di sini agar tidak konflik dengan /assets/create)
    Route::middleware(['role:admin,bendahara,kepala_sekolah,sarpras'])->group(function () {
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    });

    // Manajemen Pengguna - Hanya Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });
});
