<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PemasukanController;
use App\Http\Controllers\Api\PengeluaranController;
use App\Http\Controllers\Api\RekeningController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UtangController;
use App\Http\Controllers\Api\TransferController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (Tanpa Token) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- PROTECTED ROUTES (Butuh Token Bearer) ---
Route::middleware('auth:sanctum')->group(function () {

    // Auth & User Profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard Data
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Manajemen Rekening
    Route::apiResource('rekening', RekeningController::class);

    // Manajemen Pemasukan
    Route::apiResource('pemasukan', PemasukanController::class);

    // Manajemen Pengeluaran
    Route::apiResource('pengeluaran', PengeluaranController::class);

    // Manajemen Transfer
    Route::get('/transfer', [App\Http\Controllers\Api\TransferController::class, 'index']);
    Route::post('/transfer', [App\Http\Controllers\Api\TransferController::class, 'store']);

    // Manajemen Utang & Piutang
    Route::apiResource('utang', App\Http\Controllers\Api\UtangController::class);
    Route::post('/utang/bayar', [App\Http\Controllers\Api\UtangController::class, 'bayar']);
    Route::get('/utang/{id}/riwayat', [App\Http\Controllers\Api\UtangController::class, 'riwayat']);

    // Notifikasi
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/unread', [App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
});
