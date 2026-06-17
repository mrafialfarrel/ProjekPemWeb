<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlokasiController; 
use App\Http\Controllers\TransaksiController;

// Halaman utama (Arahkan ke file login di dalam folder auth)
Route::get('/', function () {
    return view('auth.login'); // Mencari resources/views/auth/login.blade.php
});

// Route alternatif jika ingin menggunakan welcome.blade.php
// Route::get('/welcome', function () {
//     return view('welcome'); 
// });

// Halaman Dashboard
Route::get('/dashboard', function () {
    return view('dashboard.index'); // Mencari resources/views/dashboard/index.blade.php
});

// Halaman Laporan (Report)
Route::get('/laporan', function () {
    return view('report.index'); // Mencari resources/views/report/index.blade.php
});

// Halaman Notifikasi
Route::get('/notifikasi', function () {
    return view('notification.index'); // Mencari resources/views/notification/index.blade.php
});

// Halaman Transaksi (Opsional, jika ingin menampilkan UI list transaksi)
Route::get('/transaksi', function () {
    return view('transaction.index'); // Mencari resources/views/transaction/index.blade.php
});

// ---------------------------------------------------------
// ROUTE LOGIC (Controllers)
// ---------------------------------------------------------

// Route Transaksi
Route::post('/transaksi', [TransaksiController::class, 'store']);

// Route Kantong (Allocation)
Route::get('/kantong', [AlokasiController::class, 'index']); //read
Route::post('/kantong', [AlokasiController::class, 'store']); //simpan
Route::put('/kantong/{id}', [AlokasiController::class, 'update']); //edit
Route::delete('/kantong/{id}', [AlokasiController::class, 'destroy']); //delete