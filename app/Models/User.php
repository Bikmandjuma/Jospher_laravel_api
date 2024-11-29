<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // It's better to use $fillable and avoid $guarded to prevent mass assignment vulnerabilities
    protected $fillable = [
        'names', 'provider_name', 'provider_id', 'firstname', 'lastname', 'gender',
        'email', 'phone', 'dob', 'image', 'password',
    ];

  
    // JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Return the user's ID
    }

    public function getJWTCustomClaims()
    {
        return []; // You can add custom claims here if necessary
    }
}
