<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UtangController;

Route::get('/', function () {
    return view('landing');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// Lupa / Reset Password
Route::get('/password/forgot', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/password/forgot', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// DASHBOARD & FITUR (Harus Login)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/tarik-tunai', [DashboardController::class, 'tarikTunai'])->name('dashboard.tarik');

    Route::resource('rekening', RekeningController::class);

    // --- PERBAIKAN DI SINI (Tambahkan Route Kategori) ---
    Route::post('/pemasukan/kategori', [PemasukanController::class, 'storeKategori'])->name('pemasukan.store_kategori');
    Route::resource('pemasukan', PemasukanController::class);

    Route::post('/pengeluaran/kategori', [PengeluaranController::class, 'storeKategori'])->name('pengeluaran.store_kategori');
    Route::resource('pengeluaran', PengeluaranController::class);
    Route::resource('transfer', TransferController::class);

    Route::post('/utang/bayar', [UtangController::class, 'bayar'])->name('utang.bayar');
    Route::resource('utang', UtangController::class);
    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
});
