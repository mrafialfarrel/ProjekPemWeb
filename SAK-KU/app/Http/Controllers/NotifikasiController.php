<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Alokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar notifikasi (Setara dengan state `notifications` di ViewModel)
     */
    public function index()
    {
        Notifikasi::generateProgressNotifications();
        return redirect('/dashboard');
    }

    /**
     * Mengubah status notifikasi menjadi sudah dibaca (Setara markAsRead)
     */
    public function markAsRead($id)
    {
        $notif = Notifikasi::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);

        return back();
    }

    /**
     * Opsional: Tandai semua sudah dibaca
     */
    public function markAllAsRead()
    {
        Notifikasi::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return back();
    }

    /**
     * LOGIKA BISNIS: Pengecekan Progres Kantong & Tabungan
     * (Mengadaptasi logika dari AllocationProgress.kt)
     */
    private function generateProgressNotifications()
    {
        $semuaAlokasi = Alokasi::with('transaksi')->get();

        foreach ($semuaAlokasi as $alokasi) {
            
            // Hitung uang masuk dan keluar
            $pemasukan = $alokasi->transaksi->where('is_pemasukan', true)->sum('nominal');
            $pengeluaran = $alokasi->transaksi->where('is_pemasukan', false)->sum('nominal');

            if ($alokasi->is_tabungan) {
                // --- LOGIKA TABUNGAN ---
                // Cek apakah tabungan sudah mencapai target
                if ($alokasi->target_nominal > 0 && $pemasukan >= $alokasi->target_nominal) {
                    
                    // Buat ID referensi unik (Contoh: "tabungan_success_uuid")
                    $refId = "tabungan_success_" . $alokasi->id;

                    // firstOrCreate akan mengecek: Jika $refId ini belum ada di database, maka buat baru.
                    // Jika sudah ada, abaikan (mencegah spam).
                    Notifikasi::firstOrCreate(
                        ['reference_id' => $refId],
                        [
                            'title'   => 'Tabungan Tercapai! 🎉',
                            'message' => "Selamat! Target tabungan '{$alokasi->nama}' sebesar Rp " . number_format($alokasi->target_nominal, 0, ',', '.') . " telah tercapai.",
                            'type'    => 'success'
                        ]
                    );
                }
            } else {
                // --- LOGIKA KANTONG ---
                // Cek peringatan jika pengeluaran sudah mendekati batas (misal: 90% dari batas)
                if ($alokasi->target_nominal > 0) {
                    $persentaseTerpakai = ($pengeluaran / $alokasi->target_nominal) * 100;

                    if ($persentaseTerpakai >= 90 && $persentaseTerpakai < 100) {
                        $refId = "kantong_warning_" . $alokasi->id; // Notif hampir habis
                        
                        Notifikasi::firstOrCreate(
                            ['reference_id' => $refId],
                            [
                                'title'   => 'Peringatan Kantong!',
                                'message' => "Pengeluaran untuk '{$alokasi->nama}' sudah mencapai " . round($persentaseTerpakai) . "% dari batas budget.",
                                'type'    => 'warning' // Atau 'danger' sesuai CSS Anda
                            ]
                        );
                    } elseif ($persentaseTerpakai >= 100) {
                        $refId = "kantong_overbudget_" . $alokasi->id; // Notif over budget
                        
                        Notifikasi::firstOrCreate(
                            ['reference_id' => $refId],
                            [
                                'title'   => 'Kantong Jebol! 🚨',
                                'message' => "Pengeluaran '{$alokasi->nama}' telah melebihi batas budget yang ditentukan!",
                                'type'    => 'danger' 
                            ]
                        );
                    }
                }
            }
        }
    }
}