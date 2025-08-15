<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->unsignedInteger('capacity')->default(25)->after('end_time');
            $table->index(['doctor_id', 'date', 'weekday', 'start_time'], 'doctor_sched_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropIndex('doctor_sched_lookup_idx');
            $table->dropColumn('capacity');
        });
    }
};
