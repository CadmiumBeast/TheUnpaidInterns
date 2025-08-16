<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // Filtering logic
        $query = \App\Models\DoctorSchedule::with('doctor');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }
        if ($request->filled('specialty')) {
            $query->whereHas('doctor', function($q) use ($request) {
                $q->where('specialty', $request->input('specialty'));
            });
        }
        if ($request->filled('doctor')) {
            $query->where('doctor_id', $request->input('doctor'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('doctor', function($q2) use ($search) {
                    $q2->where('full_name', 'like', "%$search%")
                        ->orWhere('specialty', 'like', "%$search%");
                })
                ->orWhere('hospital_name', 'like', "%$search%");
            });
        }

        $cards = $query->where('is_available', true)->get();
        $doctorOptions = \App\Models\Doctor::where('is_active', true)->get();

        return view('livewire.patient.appointments.index', compact('cards', 'doctorOptions'));
    }
    public function reserve(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'schedule_id' => 'required|exists:doctor_schedules,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'patient_id' => 'required|exists:users,id',
        ]);

        $appointment = Appointment::create([
            'doctor_id' => $validated['doctor_id'],
            'schedule_id' => $validated['schedule_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'patient_id' => $validated['patient_id'],
            'status' => 'pending',
        ]);

        return redirect()->route('patient.appointments.index')->with('status', 'Appointment reserved successfully!');
    }
}
