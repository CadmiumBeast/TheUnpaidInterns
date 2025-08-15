<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('doctor_schedules', function (Blueprint $table) {
			$table->id();
			$table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
			$table->string('hospital_name'); // free-text UI list
			$table->date('date')->nullable(); // for one-time entries
			$table->unsignedTinyInteger('weekday')->nullable(); // 0-6 for recurring
			$table->time('start_time');
			$table->time('end_time');
			$table->json('breaks')->nullable(); // array of {start,end}
			$table->string('recurrence_rule')->nullable(); // e.g., RRULE format or custom
			$table->boolean('is_exception')->default(false); // holidays/special clinics
			$table->boolean('is_available')->default(true); // real-time absence/unavailable
			$table->timestamps();

			$table->index(['doctor_id', 'date']);
			$table->index(['doctor_id', 'weekday']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('doctor_schedules');
	}
};
