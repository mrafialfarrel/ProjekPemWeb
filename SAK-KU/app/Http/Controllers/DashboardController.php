<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil seluruh transaksi untuk menghitung total
        // Menggunakan get() langsung karena kita butuh sum() dari keseluruhan data
        $semuaTransaksi = Transaksi::all();

        $totalPemasukan = $semuaTransaksi->where('is_pemasukan', true)->sum('nominal');
        $totalPengeluaran = $semuaTransaksi->where('is_pemasukan', false)->sum('nominal');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        // 2. Ambil 5 transaksi terbaru (Setara dengan take(5) di Kotlin)
        $recentTransactions = Transaksi::with('alokasi')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // 3. Pengaturan UI (Tema & Notifikasi)
        // Di web, kita bisa menyimpannya sementara di Cookie agar browser ingat pilihan user
        $selectedTheme = request()->cookie('theme_mode', 'system');
        $isNotificationEnabled = request()->cookie('notification_enabled', true);

        return view('dashboard.index', compact(
            'totalSaldo', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'recentTransactions',
            'selectedTheme',
            'isNotificationEnabled'
        ));
    }

    /**
     * Menyimpan preferensi tema ke Cookie browser
     */
    public function setTheme(Request $request)
    {
        $theme = $request->input('theme'); // 'light', 'dark', 'system'
        return back()->withCookie(cookie()->forever('theme_mode', $theme));
    }
}