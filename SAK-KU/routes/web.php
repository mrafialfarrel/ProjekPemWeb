<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AlokasiController; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportController;

// 1. Halaman Auth
Route::get('/', function () {
    return view('auth.login'); 
});

// 2. Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// 3. Kantong & Tabungan (Alokasi)
Route::get('/kantong', [AlokasiController::class, 'index']); 
Route::post('/kantong', [AlokasiController::class, 'store']); 
Route::put('/kantong/{id}', [AlokasiController::class, 'update']); 
Route::delete('/kantong/{id}', [AlokasiController::class, 'destroy']); 

// 4. Transaksi
Route::get('/transaksi', [TransaksiController::class, 'index']); 

Route::post('/transaksi', [TransaksiController::class, 'store']);
Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);

// 5. Laporan & Export
Route::get('/laporan', [LaporanController::class, 'index']);
Route::post('/laporan/export', [ExportController::class, 'download']);

// 6. Notifikasi
Route::get('/notifikasi', [NotifikasiController::class, 'index']);
Route::patch('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead']);
Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead']);