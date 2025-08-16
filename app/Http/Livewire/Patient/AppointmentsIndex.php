<?php

namespace App\Http\Livewire\Patient;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentsIndex extends Component
{
    public $dateFrom;
    public $specialty;
    public $doctorId;
    public $search;
    public $doctorOptions = [];
    public $cards = [];

    public function mount()
    {
        $this->dateFrom = now()->format('Y-m-d');
        $this->specialty = '';
        $this->doctorId = '';
        $this->search = '';
        $this->doctorOptions = Doctor::all();
        $this->updateCards();
    }

    public function updated($property)
    {
        $this->updateCards();
    }

    public function updateCards()
    {
        $query = \App\Models\DoctorSchedule::query()->where('is_available', true);

        if ($this->dateFrom) {
            $query->whereDate('date', $this->dateFrom);
        }
        if ($this->specialty) {
            $query->whereHas('doctor', function($q) {
                $q->where('specialty', $this->specialty);
            });
        }
        if ($this->doctorId) {
            $query->where('doctor_id', $this->doctorId);
        }
        if ($this->search) {
            $query->whereHas('doctor', function($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                  ->orWhere('registration_number', 'like', '%'.$this->search.'%');
            });
        }

        // Only show schedules for the selected date and time
        $schedules = $query->with('doctor')->get();
        $cards = [];
        foreach ($schedules as $schedule) {
            // Only show slots for the selected date
            if ($this->dateFrom && ((string)$schedule->date !== $this->dateFrom)) {
                continue;
            }
            $booked = $schedule->appointments()->count();
            $max = $schedule->capacity ?? 1;
            $cards[] = [
                'doctor' => $schedule->doctor,
                'schedule' => $schedule,
                'date' => $schedule->date,
                'booked' => $booked,
                'max' => $max,
            ];
        }
        $this->cards = $cards;
    }

    public function render()
    {
        return view('livewire.patient.appointments.index', [
            'doctorOptions' => $this->doctorOptions,
            'cards' => $this->cards,
        ]);
    }
}
