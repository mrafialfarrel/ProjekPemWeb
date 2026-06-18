<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;
use App\Models\Alokasi;

class Notifikasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'reference_id',
        'title',
        'message',
        'type',
        'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * LOGIKA BISNIS: Pengecekan Progres Kantong & Tabungan untuk membuat Notifikasi
     */
    public static function generateProgressNotifications()
    {
        $userId = Auth::id();
        if (!$userId) return;

        $semuaAlokasi = Alokasi::with('transaksi')->where('user_id', $userId)->get();

        foreach ($semuaAlokasi as $alokasi) {
            // Hitung uang masuk dan keluar
            $pemasukan = $alokasi->transaksi->where('is_pemasukan', true)->sum('nominal');
            $pengeluaran = $alokasi->transaksi->where('is_pemasukan', false)->sum('nominal');

            if ($alokasi->is_tabungan) {
                // --- LOGIKA TABUNGAN ---
                if ($alokasi->target_nominal > 0 && $pemasukan >= $alokasi->target_nominal) {
                    $refId = "tabungan_success_" . $alokasi->id;
                    self::firstOrCreate(
                        ['reference_id' => $refId, 'user_id' => $userId],
                        [
                            'title'   => 'Tabungan Tercapai! 🎉',
                            'message' => "Selamat! Target tabungan '{$alokasi->nama}' sebesar Rp " . number_format($alokasi->target_nominal, 0, ',', '.') . " telah tercapai.",
                            'type'    => 'success'
                        ]
                    );
                }
            } else {
                // --- LOGIKA KANTONG ---
                if ($alokasi->target_nominal > 0) {
                    $persentaseTerpakai = ($pengeluaran / $alokasi->target_nominal) * 100;

                    if ($persentaseTerpakai >= 90 && $persentaseTerpakai < 100) {
                        $refId = "kantong_warning_" . $alokasi->id;
                        self::firstOrCreate(
                            ['reference_id' => $refId, 'user_id' => $userId],
                            [
                                'title'   => 'Peringatan Kantong!',
                                'message' => "Pengeluaran untuk '{$alokasi->nama}' sudah mencapai " . round($persentaseTerpakai) . "% dari batas budget.",
                                'type'    => 'warning'
                            ]
                        );
                    } elseif ($persentaseTerpakai >= 100) {
                        $refId = "kantong_overbudget_" . $alokasi->id;
                        self::firstOrCreate(
                            ['reference_id' => $refId, 'user_id' => $userId],
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