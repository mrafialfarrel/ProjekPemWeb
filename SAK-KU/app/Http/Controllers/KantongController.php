<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantong;

class KantongController extends Controller
{
    // 1. Fungsi menampilkan halaman
    public function index()
    {
        $list_kantong = Kantong::where('tipe', 'kantong')->get();
        $list_tabungan = Kantong::where('tipe', 'tabungan')->get();
        $total_kekayaan = Kantong::sum('saldo');
        
        return view('allocation.index', compact('list_kantong', 'list_tabungan', 'total_kekayaan'));
    }

    // 2. Fungsi menambah data baru
    public function store(Request $request)
    {
        Kantong::create([
            'nama_kantong' => $request->nama_kantong,
            'tipe' => $request->tipe, 
            'saldo' => $request->saldo ?? 0,
            'target' => $request->target ?? null,
        ]);

        return redirect()->back();
    }

    // 3. Fungsi mengubah/update data
    public function update(Request $request, $id)
    {
        $kantong = Kantong::findOrFail($id);
        
        $kantong->update([
            'nama_kantong' => $request->nama_kantong,
            'saldo' => $request->saldo,
            'target' => $request->target ?? null,
        ]);

        return redirect()->back();
    }

    // 4. Fungsi menghapus data
    public function destroy($id)
    {
        $kantong = Kantong::findOrFail($id);
        $kantong->delete();

        return redirect()->back();
    }
}