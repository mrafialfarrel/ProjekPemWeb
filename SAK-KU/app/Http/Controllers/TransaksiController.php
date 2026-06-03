<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Kantong;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Simpan data transaksi
        Transaksi::create([
            'kantong_id' => $request->kantong_id,
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'catatan' => $request->catatan,
        ]);

        // 2. Update Saldo di tabel Kantong
        $kantong = Kantong::find($request->kantong_id);
        if ($request->jenis == 'masuk') {
            $kantong->saldo += $request->nominal;
        } else {
            $kantong->saldo -= $request->nominal;
        }
        $kantong->save();

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat!');
    }
}