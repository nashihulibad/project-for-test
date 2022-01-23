<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable  = [
       'user_id', 
       'transaksi_penjualan_id',
       'banyak_angsuran',
       'banyak_angsuran_terbayar',
       'suku_bunga',
       'is_delete'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}


