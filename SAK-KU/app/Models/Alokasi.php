<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Tambahkan ini untuk UUID

class Alokasi extends Model
{
    use HasFactory, HasUuids;

    // Karena nama model "Alokasi" tapi nama tabel "allocations"
    protected $table = 'allocations';

    // Menentukan kolom apa saja yang boleh diisi, sesuaikan dengan Migration!
    protected $fillable = [
        'nama', 
        'target_nominal', 
        'is_tabungan'
    ];

    // Opsional: Memastikan is_tabungan dibaca sebagai boolean
    protected $casts = [
        'is_tabungan' => 'boolean',
    ];

    /**
     * Relasi ke Transaksi (Satu Alokasi memiliki banyak Transaksi)
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'alokasi_id');
    }
    /**
     * ACCESSOR SALDO: Menghitung saldo secara dinamis dari relasi transaksi
     * Rumus: Pemasukan (masuk) - Pengeluaran (keluar)
     */
    public function getSaldoAttribute()
    {
        $masuk = $this->transaksi->where('jenis', 'masuk')->sum('nominal');
        $keluar = $this->transaksi->where('jenis', 'keluar')->sum('nominal');
        
        return $masuk - $keluar;
    }
}