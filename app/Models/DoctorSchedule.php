<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'hospital_name',
        'date',
        'weekday',
        'start_time',
        'end_time',
        'breaks',
        'recurrence_rule',
        'is_exception',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'breaks' => 'array',
        'is_exception' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
