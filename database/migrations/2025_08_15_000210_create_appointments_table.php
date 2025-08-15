<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('appointments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('patient_id'); // FK to patients.id (other dev handles patients table)
			$table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
			$table->date('scheduled_date');
			$table->time('start_time');
			$table->unsignedInteger('duration_minutes');
			$table->string('recurrence_pattern')->nullable();
			$table->boolean('is_recurring')->default(false);
			$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
			$table->string('status')->default('scheduled');
			$table->string('notes')->nullable();
			$table->timestamps();

			$table->index(['doctor_id', 'scheduled_date']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('appointments');
	}
};
