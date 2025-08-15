<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentReservationController extends Controller
{
    public function store(Request $request, AppointmentService $service): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id' => ['required','integer','exists:doctors,id'],
            'schedule_id' => ['required','integer','exists:doctor_schedules,id'],
            'date' => ['required','date'],
            'start_time' => ['required'],
            'patient_id' => ['nullable','integer','exists:users,id'],
        ]);

        $service->book(
            doctorId: (int)$data['doctor_id'],
            patientId: $data['patient_id'] ?? null,
            date: $data['date'],
            startTime: $data['start_time'],
            scheduleId: (int)$data['schedule_id'],
            createdBy: (int)$request->user()->id,
        );

        return back()->with('status', 'Appointment reserved');
    }
}
