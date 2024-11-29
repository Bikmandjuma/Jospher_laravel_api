<?php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
 
    protected $table = 'users';
    protected $guarded = array();

    protected $fillable = [
        'names',
        'provider_name',
        'provider_id',
        'firstname',
        'lastname',
        'gender',
        'email',
        'phone',
        'dob',
        'image',
        'password',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->password) {
                $user->password = bcrypt($user->password); // Hash the password
            }
        });
    }

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
