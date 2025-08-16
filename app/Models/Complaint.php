<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'description',
        'photo_path',
        'status',
        'assigned_to',
        'rating',
        'feedback',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->whereHas('assignedUser', function ($q) use ($department) {
            $q->where('type', $department);
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
}
