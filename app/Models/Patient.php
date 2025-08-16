<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
     protected $fillable = [
        'first_name',
        'last_name',
        'nic',
        'dob',
        'gender',
        'phone',
        'email',
        'emergency_contact',
        'address',
        'district',
        'hospital',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
