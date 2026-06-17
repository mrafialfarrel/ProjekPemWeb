<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Alokasi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menyimpan Transaksi Baru
     */
    public function store(Request $request)
    {
        // Berdasarkan form HTML Anda: 
        // <select name="jenis"> memiliki value "masuk" atau "keluar"
        $isPemasukan = $request->jenis === 'masuk';

        Transaksi::create([
            'alokasi_id'   => $request->kantong_id,
            'nominal'      => $request->nominal,
            'is_pemasukan' => $isPemasukan,
            'keterangan'   => $request->catatan ?? 'Tanpa Keterangan',
            'kategori'     => 'Umum', // Anda bisa menambahkan dropdown kategori di form HTML nanti
            'tanggal'      => now(),
        ]);

        return back();
    }
    public function index()
    {
        // Mengambil transaksi urut dari yang terbaru
        $transaksi = Transaksi::with('alokasi')->latest('tanggal')->get();
        
        // Mengambil daftar dompet/tabungan untuk form pilihan
        $list_alokasi = Alokasi::all();

        return view('transaction.index', compact('transaksi', 'list_alokasi'));
    }
}