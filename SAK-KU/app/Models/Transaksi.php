<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Tambahkan ini untuk UUID

class Transaksi extends Model
{
    use HasFactory, HasUuids;

    // Karena nama model "Transaksi" tapi nama tabel "transactions"
    protected $table = 'transactions';

    // Menentukan kolom apa saja yang boleh diisi, sesuaikan dengan Migration!
    protected $fillable = [
        'keterangan',
        'nominal',
        'is_pemasukan',
        'kategori',
        'alokasi_id',
        'tanggal'
    ];

    // Konversi tipe data secara otomatis saat ditarik dari database
    protected $casts = [
        'is_pemasukan' => 'boolean',
        'tanggal' => 'datetime', // Otomatis mengubah timestamp menjadi objek DateTime (Carbon)
    ];

    /**
     * Relasi ke Alokasi (Satu Transaksi dimiliki oleh satu Alokasi)
     */
    public function alokasi()
    {
        return $this->belongsTo(Alokasi::class, 'alokasi_id');
    }
}