<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Alokasi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * READ (Setara dengan UI State initialization di ViewModel)
     * Mengambil daftar transaksi dan daftar pilihan kantong/tabungan
     */
    public function index()
    {
        // 1. Ambil semua transaksi, diurutkan dari yang terbaru (mirip ORDER BY tanggal DESC di DAO)
        // Kita gunakan with('alokasi') agar bisa menampilkan nama kantongnya di UI nanti
        $transactions = Transaksi::with('alokasi')->orderBy('tanggal', 'desc')->get();

        // 2. Ambil semua alokasi untuk dropdown form
        $semuaAlokasi = Alokasi::all();

        // 3. Pisahkan menjadi Kantong dan Tabungan (Setara dengan listKantong & listTabungan di ViewModel)
        $listKantong = $semuaAlokasi->where('is_tabungan', false);
        $listTabungan = $semuaAlokasi->where('is_tabungan', true);

        // Kirim state ini ke view
        return view('transaction.index', compact('transactions', 'listKantong', 'listTabungan'));
    }

    /**
     * CREATE (Setara dengan addTransaction di ViewModel)
     */
    public function store(Request $request)
    {
        // Konversi input string dari HTML select ke boolean
        $isPemasukan = $request->jenis === 'masuk';

        Transaksi::create([
            'alokasi_id'   => $request->kantong_id, // UUID dari dropdown <select>
            'keterangan'   => $request->keterangan ?? 'Tanpa Keterangan',
            'nominal'      => $request->nominal,
            'is_pemasukan' => $isPemasukan,
            'kategori'     => $request->kategori ?? 'Umum',
            'tanggal'      => now(), // Setara dengan System.currentTimeMillis() di Kotlin
        ]);

        // Setelah sukses menyimpan, refresh halaman
        return back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * UPDATE (Setara dengan updateTransaction di ViewModel)
     */
    public function update(Request $request, $id)
    {
        // Cari transaksi berdasarkan UUID
        $transaksi = Transaksi::findOrFail($id);

        $isPemasukan = $request->jenis === 'masuk';

        // Update nilainya. Jika form tidak mengirimkan data baru, gunakan data lama
        $transaksi->update([
            'alokasi_id'   => $request->kantong_id ?? $transaksi->alokasi_id,
            'keterangan'   => $request->keterangan ?? $transaksi->keterangan,
            'nominal'      => $request->nominal ?? $transaksi->nominal,
            'is_pemasukan' => $request->has('jenis') ? $isPemasukan : $transaksi->is_pemasukan,
            'kategori'     => $request->kategori ?? $transaksi->kategori,
        ]);

        return back()->with('success', 'Transaksi berhasil diubah!');
    }

    /**
     * DELETE (Setara dengan deleteTransaction di ViewModel)
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dihapus!');
    }
}