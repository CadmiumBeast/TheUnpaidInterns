<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('schedule_id')->nullable()->after('doctor_id')->constrained('doctor_schedules')->nullOnDelete();
            $table->unique(['doctor_id','scheduled_date','start_time'], 'unique_doctor_slot');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('unique_doctor_slot');
            $table->dropConstrainedForeignId('schedule_id');
        });
    }
};
