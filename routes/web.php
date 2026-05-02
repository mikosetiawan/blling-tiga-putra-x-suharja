<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::resource('pelanggan', PelangganController::class);

    // billing/create harus didaftarkan sebelum resource `billing/{billing}`,
    // kalau tidak, path /billing/create dianggap {billing}=create → 404.
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');
        Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
        Route::delete('/billing/{billing}', [BillingController::class, 'destroy'])->name('billing.destroy');
    });
    Route::resource('billing', BillingController::class)->only(['index', 'show']);
    Route::post('/billing/{billing}/upload-bukti', [BillingController::class, 'uploadBukti'])->name('billing.upload-bukti');

    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/pelanggan', [LaporanController::class, 'pelanggan'])->name('pelanggan');
            Route::get('/billing', [LaporanController::class, 'billing'])->name('billing');
            Route::get('/statistik', [LaporanController::class, 'dashboard'])->name('statistik');
        });

        Route::resource('users', App\Http\Controllers\UserController::class);
    });
});

require __DIR__.'/auth.php';
