<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'manufacturer',
        'type',
        'dosage',
        'stock',
    ];

    public function pharmacies()
    {
        return $this->belongsToMany(Pharmacy::class, 'pharmacy_medicines')
            ->withPivot('stock')
            ->withTimestamps();
    }
}
