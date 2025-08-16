<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $doctorId = '';
    public $dateFrom = '';
    public $specialty = '';
    public $search = '';
    public $patientId = '';
    public $patientSearch = '';

    public function mount(): void
    {
        if (empty($this->dateFrom)) {
            $this->dateFrom = now()->toDateString();
        }
    }

    public function with(): array
    {
        $date = $this->dateFrom ?: now()->toDateString();

    $base = Doctor::query()
            ->when($this->specialty, fn($q) => $q->where('specialty', $this->specialty))
            ->when($this->search, fn($q) => $q->where(function($qq){
                $qq->where('full_name', 'like', '%'.$this->search.'%')
                   ->orWhere('license_number', 'like', '%'.$this->search.'%');
            }))
            ->where('is_active', true);

        $doctors = (clone $base)
            ->when($this->doctorId, fn($q) => $q->where('id', $this->doctorId))
            ->orderBy('full_name')
            ->get(['id','full_name','specialty','profile_photo_path']);

        $doctorOptions = (clone $base)->orderBy('full_name')->get(['id','full_name']);

        $patientOptions = collect();
        if (strlen($this->patientSearch) >= 2) {
            $term = '%'.$this->patientSearch.'%';
            $patientOptions = User::query()
                ->where('type', 'patient')
                ->where(function($q) use ($term){
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term);
                })
                ->orderBy('name')
                ->limit(10)
                ->get(['id','name','email']);
        }

        $cards = collect();
        foreach ($doctors as $doctor) {
            $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
                ->where(function($q) use ($date) {
                    $q->whereDate('date', $date)
                      ->orWhere('weekday', \Carbon\Carbon::parse($date)->dayOfWeek);
                })
                ->where('is_exception', false)
                ->orderBy('start_time')
                ->get();

            foreach ($schedules as $schedule) {
                $maxBookings = $schedule->capacity ?? 25;
                $booked = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('scheduled_date', $date)
                    ->where('start_time', $schedule->start_time)
                    ->count();

                $cards->push([
                    'doctor' => $doctor,
                    'schedule' => $schedule,
                    'booked' => $booked,
                    'max' => $maxBookings,
                    'date' => $date,
                ]);
            }
        }

    return compact('doctors','doctorOptions','cards','date','patientOptions');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold mb-4">Doctor Appointment Slots</h1>

    <div class="flex flex-wrap gap-4 mb-6 items-end">
        <div class="flex-1 min-w-[260px]">
            <label class="text-sm block mb-1">Patient</label>
            <div class="flex gap-2">
                <input type="text" class="border rounded p-2 w-full" placeholder="Search patient by name or email" wire:model.live.debounce.300ms="patientSearch" />
            </div>
            @if($patientOptions->isNotEmpty())
                <select class="mt-2 border rounded p-2 w-full" wire:model.live="patientId">
                    <option value="">Select patient…</option>
                    @foreach($patientOptions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->email }}</option>
                    @endforeach
                </select>
            @else
                <div class="mt-2 text-xs text-zinc-500">Type at least 2 characters to search patients.</div>
            @endif
        </div>
        <div>
            <label class="text-sm block mb-1">Date</label>
            <input type="date" class="border rounded p-2" wire:model.live="dateFrom" />
        </div>
        <div>
            <label class="text-sm block mb-1">All Departments</label>
            <select class="border rounded p-2" wire:model.live="specialty">
                <option value="">All Departments</option>
                @foreach(config('specialties.list', []) as $spec)
                    <option value="{{ $spec }}">{{ $spec }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm block mb-1">Doctor</label>
            <select class="border rounded p-2" wire:model.live="doctorId">
                <option value="">All</option>
                @foreach($doctorOptions as $d)
                    <option value="{{ $d->id }}">{{ $d->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[240px]">
            <label class="text-sm block mb-1">Search</label>
            <input type="text" class="border rounded p-2 w-full" placeholder="Search by name or registration..." wire:model.live.debounce.300ms="search" />
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        @if($cards->isEmpty())
            <div class="md:col-span-3 text-center text-zinc-500 py-12">No slots for the selected filters.</div>
        @endif
        @foreach($cards as $c)
        <div class="rounded-2xl border bg-white dark:bg-zinc-900 p-5 shadow">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-zinc-200 overflow-hidden">
                    @if($c['doctor']->profile_photo_path)
                        <img class="w-16 h-16 object-cover" src="{{ Storage::disk('public')->url($c['doctor']->profile_photo_path) }}" alt="Doctor"/>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-lg">{{ $c['doctor']->full_name }}</div>
                    <div class="text-sm text-zinc-500">{{ $c['doctor']->specialty }}</div>
                    <div class="mt-1 text-xs text-zinc-500">{{ $c['schedule']->hospital_name ?? 'Hospital — N/A' }}</div>
                </div>
                @php
                    $isClosed = !$c['schedule']->is_available;
                    $isFull = $c['booked'] >= $c['max'];
                @endphp
                <div class="text-xs px-3 py-1 rounded-full {{ $isClosed ? 'bg-zinc-200 text-zinc-600' : ($isFull ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') }}">
                    {{ $isClosed ? 'Closed' : ($isFull ? 'Full' : 'Open') }}
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
                <div>
                    <div class="text-zinc-500">Date</div>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($c['date'])->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="text-zinc-500">Time</div>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($c['schedule']->start_time)->format('h:i A') }}</div>
                </div>
                <div>
                    <div class="text-zinc-500">Booked</div>
                    <div class="font-medium">{{ $c['booked'] }}/{{ $c['max'] }}</div>
                </div>
            </div>
            <div class="mt-4">
                <form method="POST" action="{{ route('admin.appointments.reserve') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $c['doctor']->id }}" />
                    <input type="hidden" name="schedule_id" value="{{ $c['schedule']->id }}" />
                    <input type="hidden" name="date" value="{{ $c['date'] }}" />
                    <input type="hidden" name="start_time" value="{{ $c['schedule']->start_time }}" />
                    <input type="hidden" name="patient_id" value="{{ $patientId }}" />
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-red-500 text-white py-2.5 px-4 disabled:opacity-50" @disabled($isClosed || $isFull || !$patientId)>
                        Reserve Appointment
                    </button>
                </form>
                @if(!$patientId)
                    <div class="mt-2 text-xs text-zinc-500">Select a patient above to enable reservation.</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
