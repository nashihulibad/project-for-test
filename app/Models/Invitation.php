<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    protected $fillable  = [
       'komunitas_id', 
       'user_id', 
       'is_confirm'
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class,'komunitas_id','id');
    }
}


 