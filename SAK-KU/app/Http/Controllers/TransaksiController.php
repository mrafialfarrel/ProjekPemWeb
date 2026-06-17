<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Alokasi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Redirect to unified page
     */
    public function index()
    {
        return redirect('/kantong');
    }

    /**
     * CREATE (Setara dengan addTransaction di ViewModel)
     */
    public function store(Request $request)
    {
        $isPemasukan = false;
        if ($request->has('jenis')) {
            $isPemasukan = $request->jenis === 'masuk';
        } elseif ($request->has('is_pemasukan')) {
            $isPemasukan = $request->is_pemasukan == '1';
        }

        $alokasiId = $request->input('alokasi_id') ?? $request->input('kantong_id');

        Transaksi::create([
            'alokasi_id'   => $alokasiId,
            'keterangan'   => $request->keterangan ?? 'Tanpa Keterangan',
            'nominal'      => $request->nominal,
            'is_pemasukan' => $isPemasukan,
            'kategori'     => $request->kategori ?? 'Umum',
            'tanggal'      => now(),
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    /**
     * UPDATE (Setara dengan updateTransaction di ViewModel)
     */
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $isPemasukan = $transaksi->is_pemasukan;
        if ($request->has('jenis')) {
            $isPemasukan = $request->jenis === 'masuk';
        } elseif ($request->has('is_pemasukan')) {
            $isPemasukan = $request->is_pemasukan == '1';
        }

        $alokasiId = $request->input('alokasi_id') ?? $request->input('kantong_id') ?? $transaksi->alokasi_id;

        $transaksi->update([
            'alokasi_id'   => $alokasiId,
            'keterangan'   => $request->keterangan ?? $transaksi->keterangan,
            'nominal'      => $request->nominal ?? $transaksi->nominal,
            'is_pemasukan' => $isPemasukan,
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