<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransaksiController; // Tambahkan ini

/* ... route default ... */

Route::apiResource('transaksi', TransaksiController::class);
