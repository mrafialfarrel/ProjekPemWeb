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
        // Tarik semua Alokasi sekaligus riwayat transaksinya dari Database, urutkan berdasarkan sort_order
        $semuaAlokasi = Alokasi::with('transaksi')->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc')->get();

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

        // Tarik semua transaksi, diurutkan dari yang terbaru
        $transaksi = Transaksi::with('alokasi')->orderBy('tanggal', 'desc')->get();

        // Kirim data yang sudah diolah ke UI Blade
        return view('allocation.index', compact('list_kantong', 'list_tabungan', 'total_kekayaan', 'transaksi', 'semuaAlokasi'));
    }

    /**
     * Menyimpan Alokasi Baru (Kantong / Tabungan)
     * (Setara dengan addAllocation event di ViewModel)
     */
    public function store(Request $request)
    {
        $nama = $request->input('nama') ?? $request->input('nama_kantong');
        $target = $request->input('target_nominal') ?? $request->input('target') ?? 0;
        $isTabungan = $request->input('is_tabungan') == '1' || ($request->has('tipe') && $request->tipe === 'tabungan');

        $maxSortOrder = Alokasi::where('is_tabungan', $isTabungan)->max('sort_order') ?? 0;

        $alokasi = Alokasi::create([
            'nama'           => $nama,
            'target_nominal' => $target, 
            'is_tabungan'    => $isTabungan,
            'sort_order'     => $maxSortOrder + 1
        ]);

        // Inisiasi Saldo Awal sebagai Transaksi Pemasukan
        $saldoAwal = $request->input('saldo_awal') ?? $request->input('saldo') ?? 0;
        if ($saldoAwal > 0) {
            $alokasi->transaksi()->create([
                'keterangan'   => 'Saldo Awal',
                'nominal'      => $saldoAwal,
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
        
        $nama = $request->input('nama') ?? $request->input('nama_kantong') ?? $alokasi->nama;
        $target = $request->input('target_nominal') ?? $request->input('target') ?? $alokasi->target_nominal;

        $alokasi->update([
            'nama'           => $nama,
            'target_nominal' => $target,
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

    /**
     * Memindahkan urutan alokasi (up / down)
     */
    public function move(Request $request, $id)
    {
        $alokasi = Alokasi::findOrFail($id);
        $direction = $request->input('direction'); // 'up' or 'down'
        $isTabungan = $alokasi->is_tabungan;

        // Ambil semua alokasi dengan tipe yang sama diurutkan berdasarkan sort_order
        $items = Alokasi::where('is_tabungan', $isTabungan)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Cari index item saat ini
        $index = $items->search(function ($item) use ($id) {
            return $item->id === $id;
        });

        if ($index !== false) {
            if ($direction === 'up' && $index > 0) {
                $swapWith = $items[$index - 1];
                $temp = $alokasi->sort_order;
                
                if ($temp == $swapWith->sort_order) {
                    $alokasi->sort_order = $temp - 1;
                } else {
                    $alokasi->sort_order = $swapWith->sort_order;
                    $swapWith->sort_order = $temp;
                }
                
                $alokasi->save();
                $swapWith->save();
            } elseif ($direction === 'down' && $index < count($items) - 1) {
                $swapWith = $items[$index + 1];
                $temp = $alokasi->sort_order;
                
                if ($temp == $swapWith->sort_order) {
                    $alokasi->sort_order = $temp + 1;
                } else {
                    $alokasi->sort_order = $swapWith->sort_order;
                    $swapWith->sort_order = $temp;
                }
                
                $alokasi->save();
                $swapWith->save();
            }

            // Normalisasi urutan agar tersusun rapi dari 0, 1, 2, ...
            $items = Alokasi::where('is_tabungan', $isTabungan)
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            foreach ($items as $idx => $item) {
                $item->sort_order = $idx;
                $item->save();
            }
        }

        return back();
    }
}