<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\UtangController;
use App\Http\Controllers\TransferController;

Route::get('/', function () {
    return redirect()->route('login');
});

// === INI BAGIAN YANG PENTING UNTUK DIPERBAIKI ===
// Route Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route Register (Pastikan ->name('register') ada!)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// ==============================================
// Protected Routes (Butuh Login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/tarik-tunai', [DashboardController::class, 'tarikTunai'])->name('dashboard.tarik');
    
    Route::resource('rekening', RekeningController::class);
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('pengeluaran', PengeluaranController::class);
    Route::resource('transfer', TransferController::class);
    Route::resource('utang', UtangController::class);
    Route::post('/utang/bayar', [UtangController::class, 'bayar'])->name('utang.bayar');
});