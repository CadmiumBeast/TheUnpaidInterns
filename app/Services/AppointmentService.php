<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Book an appointment into a schedule slot with capacity + overlap checks.
     *
     * @param int $doctorId
     * @param int|null $patientId
     * @param string $date YYYY-MM-DD
     * @param string $startTime HH:MM
     * @param int $scheduleId
     * @param int $createdBy
     * @param int $durationMinutes
     */
    public function book(int $doctorId, ?int $patientId, string $date, string $startTime, int $scheduleId, int $createdBy, int $durationMinutes = 15): Appointment
    {
        return DB::transaction(function () use ($doctorId, $patientId, $date, $startTime, $scheduleId, $createdBy, $durationMinutes) {
            $schedule = DoctorSchedule::lockForUpdate()->findOrFail($scheduleId);

            if (!$schedule->is_available) {
                throw ValidationException::withMessages(['slot' => 'This slot is unavailable.']);
            }

            if ($schedule->doctor_id !== $doctorId) {
                throw ValidationException::withMessages(['slot' => 'Schedule mismatch.']);
            }

            // Absence conflict: block if an exception entry covers this date/weekday
            $day = \Illuminate\Support\Carbon::parse($date);
            $absence = DoctorSchedule::where('doctor_id', $doctorId)
                ->where('is_exception', true)
                ->where(function($q) use ($day) {
                    $q->whereDate('date', $day->toDateString())
                      ->orWhere('weekday', $day->dayOfWeek);
                })
                ->exists();
            if ($absence) {
                throw ValidationException::withMessages(['slot' => 'Doctor is on leave for this time.']);
            }

            // Cross-hospital duplicate time: ensure no other schedule at same time for this doctor
            $dupTime = DoctorSchedule::where('doctor_id', $doctorId)
                ->where('id', '!=', $scheduleId)
                ->where('start_time', $startTime)
                ->where(function($q) use ($day) {
                    $q->whereDate('date', $day->toDateString())
                      ->orWhere('weekday', $day->dayOfWeek);
                })
                ->exists();
            if ($dupTime) {
                throw ValidationException::withMessages(['slot' => 'Doctor already has a slot at this time.']);
            }

            // Capacity check
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('scheduled_date', $date)
                ->where('start_time', $startTime)
                ->count();

            if ($bookedCount >= ($schedule->capacity ?? 25)) {
                throw ValidationException::withMessages(['slot' => 'This slot is full.']);
            }

            // Overlap check: ensure no overlapping appointment for same doctor at that time
            $existsOverlap = Appointment::where('doctor_id', $doctorId)
                ->whereDate('scheduled_date', $date)
                ->where('start_time', $startTime)
                ->exists();

            if ($existsOverlap) {
                throw ValidationException::withMessages(['slot' => 'This time is already taken.']);
            }

            return Appointment::create([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'schedule_id' => $scheduleId,
                'scheduled_date' => $date,
                'start_time' => $startTime,
                'duration_minutes' => $durationMinutes,
                'created_by' => $createdBy,
                'status' => 'booked',
            ]);
        });
    }
}
