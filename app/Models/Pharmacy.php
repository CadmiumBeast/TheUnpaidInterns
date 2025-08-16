<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'location',
        'phone_number',
    ];

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'pharmacy_medicines')
            ->withPivot('stock')
            ->withTimestamps();
    }
}
