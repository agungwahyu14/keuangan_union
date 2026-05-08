<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Manajemen Keuangan
|--------------------------------------------------------------------------
| Middleware stack:
|   auth   → harus login
|   active → user.is_active = true (CheckUserActive)
|   role:admin → hanya role Admin (Spatie)
*/

// ── Root redirect ─────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->hasRole('admin')
            ? redirect()->route('dashboard')
            : redirect()->route('transaksi.index');
    }
    return redirect()->route('login');
});

// ── Dashboard ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ── Transaksi (Auth semua role) ───────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::resource('transaksi', TransactionController::class)
        ->parameters(['transaksi' => 'transaksi']);

    // Admin-only: hapus transaksi
    Route::delete('transaksi/{transaksi}/force', [TransactionController::class, 'destroy'])
        ->name('transaksi.force-destroy')
        ->middleware('permission:delete-transactions');
});

// ── Kategori (Admin only) ─────────────────────────────────────────────────
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::resource('kategori', CategoryController::class)
        ->parameters(['kategori' => 'kategori']);

    Route::patch('kategori/{kategori}/toggle', [CategoryController::class, 'toggle'])
        ->name('kategori.toggle');
});

// ── Manajemen Pengguna (Admin only) ──────────────────────────────────────
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::resource('pengguna', UserManagementController::class)
        ->parameters(['pengguna' => 'pengguna']);

    Route::patch('pengguna/{pengguna}/toggle-active', [UserManagementController::class, 'toggleActive'])
        ->name('pengguna.toggle-active');
});

// ── Laporan (Admin only) ────────────────────────────────────────────────────
Route::middleware(['auth', 'active', 'permission:view-reports'])->prefix('laporan')->name('laporan.')->group(function () {
    // Halaman laporan web
    Route::get('/arus-kas',   [LaporanController::class, 'arusKas'])->name('arus-kas');
    Route::get('/laba-rugi',  [LaporanController::class, 'labaRugi'])->name('laba-rugi');

    // Export Excel (membutuhkan permission export-reports)
    Route::get('/export/excel/{type}', [LaporanController::class, 'exportExcel'])
        ->name('export.excel')
        ->middleware('permission:export-reports');

    // Export PDF (membutuhkan permission export-reports)
    Route::get('/export/pdf/{type}', [LaporanController::class, 'exportPdf'])
        ->name('export.pdf')
        ->middleware('permission:export-reports');
});

// ── Profile (semua user auth) ─────────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
