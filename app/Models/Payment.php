<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_id',
        'phone',
        'amount',
        'duration', // Duration in months
        'start_date',
        'end_date',
        'active_days',
        'status',
    ];

    protected $dates = ['start_date', 'end_date'];

    /**
     * Boot method to set the start_date, end_date, and active_days automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $currentDate = Carbon::now();
            $payment->start_date = $currentDate;
            $payment->end_date = $currentDate->copy()->addMonths($payment->duration);
            $payment->active_days = $payment->duration * 30; // Approximate days
        });
    }

    /**
     * Check if the payment is currently active.
     */
    public function isActive()
    {
        return now()->between($this->start_date, $this->end_date);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
