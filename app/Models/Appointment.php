<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
    'schedule_id',
        'scheduled_date',
        'start_time',
        'duration_minutes',
        'recurrence_pattern',
        'is_recurring',
        'created_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    // store start_time as time string HH:MM
    'start_time' => 'string',
        'is_recurring' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class, 'schedule_id');
    }
}
