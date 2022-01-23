<?php

namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $fillable  = [
        'user_id',
        'nama',  
        'deskripsi',
        'kategori',
        'harga_jual',
        'harga_beli',
        'stok',
        'terjual',
        'is_delete'
    ];
}   