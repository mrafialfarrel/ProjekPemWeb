<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KantongController; // Tambahkan ini di atas
use App\Http\Controllers\TransaksiController;

Route::post('/transaksi', [TransaksiController::class, 'store']);

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// Ubah route Kantong menjadi seperti ini:
Route::get('/kantong', [KantongController::class, 'index']); // Untuk nampilin halaman
Route::post('/kantong', [KantongController::class, 'store']); // Untuk nyimpen data form

Route::get('/laporan', function () {
    return view('laporan');
});

Route::get('/notifikasi', function () {
    return view('notifikasi');
});

// Rute yang sudah ada sebelumnya
Route::get('/kantong', [KantongController::class, 'index']);
Route::post('/kantong', [KantongController::class, 'store']);

// TAMBAHKAN DUA BARIS INI:
Route::put('/kantong/{id}', [KantongController::class, 'update']); // Untuk edit/update
Route::delete('/kantong/{id}', [KantongController::class, 'destroy']); // Untuk hapus