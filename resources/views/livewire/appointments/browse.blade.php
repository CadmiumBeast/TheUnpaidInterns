<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $doctorId = '';
    public $date = '';

    public function mount(): void
    {
        $this->date = $this->date ?: now()->toDateString();
    }

    public function with(): array
    {
        $date = $this->date ?: now()->toDateString();

        $doctors = Doctor::query()
            ->when($this->doctorId, fn($q) => $q->where('id', $this->doctorId))
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
    <h1 class="text-2xl font-semibold mb-4">Book an Appointment</h1>

    <div class="flex flex-wrap gap-4 mb-6">
        <flux:input type="date" wire:model="date" label="Date" />
        <flux:select wire:model="doctorId" label="Doctor">
            <option value="">All</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}">{{ $d->full_name }}</option>
            @endforeach
        </flux:select>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach($cards as $c)
        <div class="rounded-xl border bg-white dark:bg-zinc-900 p-4 shadow">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-zinc-200 overflow-hidden">
                    @if($c['doctor']->profile_photo_path)
                        <img class="w-14 h-14 object-cover" src="{{ Storage::disk('public')->url($c['doctor']->profile_photo_path) }}" alt="Doctor"/>
                    @endif
                </div>
                <div>
                    <div class="font-semibold">{{ $c['doctor']->full_name }}</div>
                    <div class="text-sm text-zinc-500">{{ $c['doctor']->specialty }}</div>
                </div>
            </div>
            <div class="mt-3 text-sm text-zinc-600">
                <div>{{ $c['schedule']->hospital_name ?? 'Hospital — N/A' }}</div>
                <div>{{ $c['date'] }}</div>
                <div>{{ $c['schedule']->start_time }} - {{ $c['schedule']->end_time }}</div>
                <div>{{ $c['booked'] }}/{{ $c['max'] }} Booked ({{ max($c['max'] - $c['booked'], 0) }} left)</div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div class="text-xs {{ $c['booked'] < $c['max'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ $c['booked'] < $c['max'] ? 'Available' : 'Full' }}
                </div>
                <flux:button variant="primary" :disabled="$c['booked'] >= $c['max']">Reserve</flux:button>
            </div>
        </div>
        @endforeach
    </div>
</div>
