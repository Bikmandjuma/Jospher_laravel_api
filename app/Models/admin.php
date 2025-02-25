<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Define the table associated with the model (if not following Laravel's convention)
    protected $table = 'admins';
    protected $guard = 'admin';

    // Define which attributes are mass assignable
    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'phone',
        'email',
        'role',
        'image',
        'dob',
        'password',
    ];

    // Protect against mass-assignment vulnerability
    protected $guarded = [];

    // Attributes that should be hidden when the model is converted to an array or JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts certain attributes to native types
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date', // Assuming 'dob' is a date column in the database
    ];

    // Optionally, define timestamps if not using the default 'created_at' and 'updated_at'
    // public $timestamps = false;

    // If you want to make any additional custom methods or attributes, you can do that here.
}
