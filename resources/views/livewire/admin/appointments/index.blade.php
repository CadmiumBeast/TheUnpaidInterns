<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $doctorId = '';
    public $dateFrom = '';
    public $specialty = '';
    public $search = '';

    public function with(): array
    {
        $date = $this->dateFrom ?: now()->toDateString();

        $doctors = Doctor::query()
            ->when($this->doctorId, fn($q) => $q->where('id', $this->doctorId))
            ->when($this->specialty, fn($q) => $q->where('specialty', $this->specialty))
            ->when($this->search, fn($q) => $q->where(function($qq){
                $qq->where('full_name', 'like', '%'.$this->search.'%')
                   ->orWhere('license_number', 'like', '%'.$this->search.'%');
            }))
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get(['id','full_name','specialty','profile_photo_path']);

        $cards = collect();
        foreach ($doctors as $doctor) {
            $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
                ->where(function($q) use ($date) {
                    $q->whereDate('date', $date)
                      ->orWhere('weekday', \Carbon\Carbon::parse($date)->dayOfWeek);
                })
                ->where('is_available', true)
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

        return compact('doctors','cards','date');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold mb-4">Doctor Appointment Slots</h1>

    <div class="flex flex-wrap gap-4 mb-6 items-end">
        <div>
            <label class="text-sm block mb-1">Date</label>
            <input type="date" class="border rounded p-2" wire:model="dateFrom" />
        </div>
        <div>
            <label class="text-sm block mb-1">All Departments</label>
            <select class="border rounded p-2" wire:model="specialty">
                <option value="">All Departments</option>
                @foreach(config('specialties.list', []) as $spec)
                    <option value="{{ $spec }}">{{ $spec }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm block mb-1">Doctor</label>
            <select class="border rounded p-2" wire:model="doctorId">
                <option value="">All</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}">{{ $d->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[240px]">
            <label class="text-sm block mb-1">Search</label>
            <input type="text" class="border rounded p-2 w-full" placeholder="Search by name or registration..." wire:model.debounce.300ms="search" />
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
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
                <div class="text-xs px-3 py-1 rounded-full {{ $c['booked'] < $c['max'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $c['booked'] < $c['max'] ? 'Open' : 'Full' }}
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
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-red-500 text-white py-2.5 px-4 disabled:opacity-50" @disabled($c['booked'] >= $c['max'])>
                        Reserve Appointment
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
