<?php

namespace App\Http\Controllers;

use App\Models\Alokasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class AlokasiController extends Controller
{
    /**
     * Menampilkan halaman Kantong & Tabungan (Setara dengan mengambil data dari Flow DAO)
     */
    public function index()
    {
        // Mengambil semua alokasi beserta data transaksinya (Eager Loading untuk performa)
        $semuaAlokasi = Alokasi::with('transaksi')->get();

        // Kita siapkan penampung datanya
        $list_kantong = collect();
        $list_tabungan = collect();
        $total_kekayaan = 0;

        foreach ($semuaAlokasi as $alokasi) {
            // 1. Hitung Saldo secara dinamis dari tabel transaksi
            $pemasukan = $alokasi->transaksi->where('is_pemasukan', true)->sum('nominal');
            $pengeluaran = $alokasi->transaksi->where('is_pemasukan', false)->sum('nominal');
            $saldo = $pemasukan - $pengeluaran;

            // 2. Tambahkan properti 'saldo' secara on-the-fly agar bisa dibaca di file .blade.php
            $alokasi->saldo = $saldo;
            
            // 3. Tambahkan ke total kekayaan keseluruhan
            $total_kekayaan += $saldo;

            // 4. Pisahkan mana yang Kantong, mana yang Tabungan
            if ($alokasi->is_tabungan) {
                $list_tabungan->push($alokasi);
            } else {
                $list_kantong->push($alokasi);
            }
        }

        // Kirim semua data ke View yang ada di folder 'allocation'
        return view('allocation.index', compact('list_kantong', 'list_tabungan', 'total_kekayaan'));
    }

    /**
     * Menyimpan Kantong/Tabungan Baru
     */
    public function store(Request $request)
    {
        // Cek apakah request datang dari form tabungan (ada input hidden name="tipe" value="tabungan")
        $isTabungan = $request->has('tipe') && $request->tipe === 'tabungan';

        // Buat Alokasi Baru
        $alokasi = Alokasi::create([
            'nama'           => $request->nama_kantong,
            'target_nominal' => $request->target ?? 0, // Jika null, default 0
            'is_tabungan'    => $isTabungan
        ]);

        // Jika user mengisi "Saldo Awal" dari form, kita catat sebagai TRANSAKSI PERTAMA!
        if ($request->filled('saldo') && $request->saldo > 0) {
            $alokasi->transaksi()->create([
                'keterangan'   => 'Saldo Awal',
                'nominal'      => $request->saldo,
                'is_pemasukan' => true,
                'kategori'     => 'Penyesuaian',
                'tanggal'      => now(), // Waktu saat ini
            ]);
        }

        // Kembali ke halaman sebelumnya
        return back();
    }

    /**
     * Mengubah Data (Update)
     */
    public function update(Request $request, $id)
    {
        $alokasi = Alokasi::findOrFail($id);
        
        $alokasi->update([
            'nama'           => $request->nama_kantong ?? $alokasi->nama,
            'target_nominal' => $request->target ?? $alokasi->target_nominal,
        ]);

        /* * Catatan: Jika ingin fitur 'Ubah Saldo Saat Ini' berfungsi, 
         * Anda harus membuat logika yang menambahkan transaksi penyesuaian (adjustment).
         * Tidak bisa sekadar mengupdate kolom, karena saldo didapat dari transaksi.
         */

        return back();
    }

    /**
     * Menghapus Data (Delete)
     */
    public function destroy($id)
    {
        $alokasi = Alokasi::findOrFail($id);
        
        // Karena di Migration kita set 'nullOnDelete()', transaksi tidak akan terhapus, 
        // hanya ID alokasinya yang jadi null (History tetap aman!)
        $alokasi->delete();

        return back();
    }
}