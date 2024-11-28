<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $table='admins';
    protected $guarded = array();
    
    use HasFactory,Notifiable;
    protected $fillable = [
        'socialite_user_id',
        'names',
        'firstname',
        'lastname',
        'email',
        'phone',
        'dob',
        'image',
        'username',
        'password',
    ];

    public function socialiteUser()
    {
        return $this->belongsTo(SocialiteUser::class, 'socialite_user_id');
    }
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Implement the required methods for JWTSubject interface

    /**
     * Get the identifier that will be stored in the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // or return the primary key field
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

