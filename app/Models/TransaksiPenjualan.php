<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable  = [
       'user_id', 
       'produk_id',
       'qty', 
       'harga',
       'total_harga',
       'nama_pembeli',
       'is_kredit',
       'keterangan',
       'year_month_date',
       'is_delete'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class,'produk_id','id');
    }
}


