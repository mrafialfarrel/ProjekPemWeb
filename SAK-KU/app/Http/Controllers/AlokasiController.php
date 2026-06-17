<?php

namespace App\Http\Controllers;

use App\Models\Alokasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class AlokasiController extends Controller
{
    /**
     * Menampilkan halaman Kantong & Tabungan 
     * (Mengadaptasi logika dari 'savings' dan 'pockets' StateFlow)
     */
    public function index()
    {
        // Tarik semua Alokasi sekaligus riwayat transaksinya dari Database
        $semuaAlokasi = Alokasi::with('transaksi')->get();

        $list_kantong = collect();
        $list_tabungan = collect();
        $total_kekayaan = 0;

        foreach ($semuaAlokasi as $alokasi) {
            // Pemisahan Pemasukan dan Pengeluaran berdasarkan relasi transaksi
            $total_pemasukan = $alokasi->transaksi->where('is_pemasukan', true)->sum('nominal');
            $total_pengeluaran = $alokasi->transaksi->where('is_pemasukan', false)->sum('nominal');
            
            // Saldo Aktual = Total Masuk - Total Keluar
            // Kita tetap hitung ini untuk mendapatkan "Total Kekayaan" secara keseluruhan
            $saldo_aktual = $total_pemasukan - $total_pengeluaran;
            $alokasi->saldo = $saldo_aktual; 
            $total_kekayaan += $saldo_aktual;

            // Logika Pemisahan Sesuai AllocationViewModel.kt
            if ($alokasi->is_tabungan) {
                // TABUNGAN (Savings StateFlow)
                // Fokus pada target dan uang yang terkumpul (Pemasukan)
                $alokasi->terkumpul = $total_pemasukan; // Setara dengan 'currentAmount' di Kotlin
                $alokasi->target = $alokasi->target_nominal;
                
                $list_tabungan->push($alokasi);
            } else {
                // KANTONG (Pockets StateFlow)
                // Fokus pada batas pengeluaran dan uang yang terpakai (Pengeluaran)
                $alokasi->terpakai = $total_pengeluaran; // Setara dengan 'spentAmount' di Kotlin
                $alokasi->batas_budget = $alokasi->target_nominal; // Setara dengan 'limit' di Kotlin
                
                $list_kantong->push($alokasi);
            }
        }

        // Kirim data yang sudah diolah ke UI Blade
        return view('allocation.index', compact('list_kantong', 'list_tabungan', 'total_kekayaan'));
    }

    /**
     * Menyimpan Alokasi Baru (Kantong / Tabungan)
     * (Setara dengan addAllocation event di ViewModel)
     */
    public function store(Request $request)
    {
        $isTabungan = $request->has('tipe') && $request->tipe === 'tabungan';

        $alokasi = Alokasi::create([
            'nama'           => $request->nama_kantong,
            'target_nominal' => $request->target ?? 0, 
            'is_tabungan'    => $isTabungan
        ]);

        // Inisiasi Saldo Awal sebagai Transaksi Pemasukan (Opsional, tergantung UI form)
        if ($request->filled('saldo') && $request->saldo > 0) {
            $alokasi->transaksi()->create([
                'keterangan'   => 'Saldo Awal',
                'nominal'      => $request->saldo,
                'is_pemasukan' => true,
                'kategori'     => 'Penyesuaian',
                'tanggal'      => now(), 
            ]);
        }

        return back();
    }

    /**
     * Mengubah Data (Update)
     * (Setara dengan updateAllocation event di ViewModel)
     */
    public function update(Request $request, $id)
    {
        $alokasi = Alokasi::findOrFail($id);
        
        $alokasi->update([
            'nama'           => $request->nama_kantong ?? $alokasi->nama,
            'target_nominal' => $request->target ?? $alokasi->target_nominal,
        ]);

        return back();
    }

    /**
     * Menghapus Data (Delete)
     * (Setara dengan deleteAllocation event di ViewModel)
     */
    public function destroy($id)
    {
        $alokasi = Alokasi::findOrFail($id);
        
        // Menghapus data kantong. Karena ada nullOnDelete() di database, 
        // histori transaksinya akan tetap ada tanpa alokasi_id.
        $alokasi->delete();

        return back();
    }
}