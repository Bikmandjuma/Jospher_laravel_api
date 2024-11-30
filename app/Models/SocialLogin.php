<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLogin extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_names',
        'provider_name',
        'provider_id',
        'email',
        'image',
    ];

}
