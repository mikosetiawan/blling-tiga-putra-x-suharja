<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (Breeze)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',           [ProfileController::class, 'edit'])           ->name('edit');
        Route::patch('/',         [ProfileController::class, 'update'])         ->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword']) ->name('password');
        Route::delete('/',        [ProfileController::class, 'destroy'])        ->name('destroy');
    });

    // Pelanggan
    Route::resource('pelanggan', PelangganController::class);

    // Billing
    Route::resource('billing', BillingController::class)->except(['edit', 'update']);
    Route::post('/billing/{billing}/upload-bukti', [BillingController::class, 'uploadBukti'])->name('billing.upload-bukti');

    // Helpdesk
    Route::resource('helpdesk', HelpdeskController::class)->except(['edit', 'destroy']);

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/pelanggan', [LaporanController::class, 'pelanggan'])->name('pelanggan');
        Route::get('/billing', [LaporanController::class, 'billing'])->name('billing');
        Route::get('/helpdesk', [LaporanController::class, 'helpdesk'])->name('helpdesk');
        Route::get('/statistik', [LaporanController::class, 'dashboard'])->name('statistik');
    });

    // Manajemen User
    Route::resource('users', App\Http\Controllers\UserController::class);
});

require __DIR__.'/auth.php';