<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AlokasiController; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportController;

// 1. Halaman Auth
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::redirect('/', '/dashboard');

// 2. Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// 3. Kantong & Tabungan (Alokasi) - READ
Route::get('/kantong', [AlokasiController::class, 'index']); 

// 4. Transaksi - READ
Route::get('/transaksi', [TransaksiController::class, 'index']); 

// 5. Laporan & Export - READ
Route::get('/laporan', [LaporanController::class, 'index']);

// 6. Notifikasi - READ
Route::get('/notifikasi', [NotifikasiController::class, 'index']);

// === WRITE ACTIONS (Protected by not.guest) ===
Route::middleware(['not.guest'])->group(function () {
    // Alokasi Write
    Route::post('/kantong', [AlokasiController::class, 'store']); 
    Route::put('/kantong/{id}', [AlokasiController::class, 'update']); 
    Route::delete('/kantong/{id}', [AlokasiController::class, 'destroy']); 
    Route::post('/kantong/{id}/move', [AlokasiController::class, 'move']); 

    // Transaksi Write
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);

    // Laporan Export
    Route::post('/laporan/export', [ExportController::class, 'download']);

    // Notifikasi Write
    Route::patch('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead']);
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead']);
});