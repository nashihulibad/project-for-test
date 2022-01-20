<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $fillable  = [
      'user_id','komunitas_id', 'is_admin'
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class,'komunitas_id','id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}


