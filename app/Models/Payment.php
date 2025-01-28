<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'phone',
        'amount',
        'status'
        'user_id',
        'duration',
        'start_date',
        'end_date',
        'active_days'
    ];

    public function user_idfn(){
        return $this->belongTo(User::class,'user_id');
    }
}
