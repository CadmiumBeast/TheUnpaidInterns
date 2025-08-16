<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyMedicine extends Model
{
    protected $table = 'pharmacy_medicines';

    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'stock',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
