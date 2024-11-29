<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SocialiteUser extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'names', 'provider_name', 'provider_id', 'firstname', 'lastname', 'gender',
        'email', 'phone', 'dob', 'image', 'password',
    ];

}
