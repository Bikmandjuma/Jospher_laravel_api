<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class SocialLogin extends Model
{
    use HasFactory, HasApiTokens;

    // The name of the table associated with this model (optional if table name is plural)
    protected $table = 'social_logins';

    // Attributes that are mass assignable
    protected $fillable = [
        'user_names',
        'provider_name',
        'provider_id',
        'email',
        'image',
    ];

}
