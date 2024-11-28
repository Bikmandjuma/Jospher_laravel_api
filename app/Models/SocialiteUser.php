<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SocialiteUser extends Authenticatable implements JWTSubject
{
    use HasFactory;

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'names',
        'provider_name',
        'provider_id',
        'provider_email',
    ];

     public function users()
    {
        return $this->hasOne(User::class);
    }
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically, this is the primary key
    }

    /**
     * Return a key-value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
