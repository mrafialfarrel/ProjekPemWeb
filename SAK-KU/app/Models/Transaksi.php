<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['kantong_id', 'jenis', 'nominal', 'catatan'];
}
