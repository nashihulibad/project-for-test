<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable  = [
       'user_id', 
       'iuran_id',
       'date', 
       'month', 
       'year', 
       'year_month_date',
       'nominal', 
       'kategori', 
       'keterangan', 
       'in_or_out', 
       'is_delete',
       'is_confirm_for_iuran'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}


