<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\AlokasiController; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AuthController;

// 1. Halaman Auth
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::redirect('/', '/dashboard');

// Middleware global untuk halaman public (Read-Only)
// Memaksa user yang login tapi belum verify untuk verify dulu. Guest tetap bisa lewat.
Route::middleware(\App\Http\Middleware\EnsureVerifiedIfLoggedIn::class)->group(function () {
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
});

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Pengecekan verifikasi untuk user yang login
Route::middleware(['auth', 'verified'])->group(function () {
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

Route::get('/clear-cache-rahasia', function() {
    Artisan::call('optimize:clear');
    return 'Cache server berhasil dibersihkan!';
});