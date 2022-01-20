<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;
    protected $fillable  = [
      'user_id',
      'notif1',
      'notif2',
      'notif3',
      'year_month_date'
    ];
}

