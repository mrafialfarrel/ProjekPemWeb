<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kantong extends Model
{
    use HasFactory;

    // Menentukan kolom apa saja yang boleh diisi (wajib ditambahkan)
    protected $fillable = ['nama_kantong', 'tipe', 'saldo', 'target'];
}