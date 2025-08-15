<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'full_name',
		'specialty',
		'license_number',
		'contact_number',
		'email',
		'schedule_notes',
		'is_active',
		'profile_photo_path',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
