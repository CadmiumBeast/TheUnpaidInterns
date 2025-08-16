<?php

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $today;
    public ?int $doctorId = null;
    public $doctor;
    public array $doctorHospitals = [];

    // Quick slot form
    public array $hospitals = [];
    public string $hospital_name = '';
    public string $slot_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public int $capacity = 25;

    // Upcoming schedule (next 7 days)
    public array $nextWeek = [];

    public function mount(): void
    {
        $this->today = now()->toDateString();
        $user = auth()->user();
        $this->doctor = \App\Models\Doctor::where('user_id', $user?->id)->first();
        $this->doctorId = $this->doctor?->id;
        $this->hospitals = config('hospitals.list', []);
        $this->slot_date = $this->today;
        $this->hydrateWeek();
    }

    public function with(): array
    {
    $id = $this->doctorId ?? 0;

    $todayAppointments = Appointment::with(['doctor','patient'])
            ->whereDate('scheduled_date', $this->today)
            ->where('doctor_id', $id)
            ->orderBy('start_time')
            ->get();

    $upcoming = Appointment::with(['doctor','patient'])
            ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->where('doctor_id', $id)
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        $this->doctorHospitals = \App\Models\DoctorSchedule::where('doctor_id', $id)
            ->select('hospital_name')
            ->distinct()
            ->pluck('hospital_name')
            ->filter()
            ->values()
            ->toArray();

        return compact('todayAppointments', 'upcoming');
    }

    public function hydrateWeek(): void
    {
        $start = \Illuminate\Support\Carbon::parse($this->today)->startOfWeek();
        $this->nextWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $day = (clone $start)->addDays($i);
            $entries = \App\Models\DoctorSchedule::where('doctor_id', $this->doctorId)
                ->where(function($q) use ($day) {
                    $q->whereDate('date', $day->toDateString())
                      ->orWhere('weekday', $day->dayOfWeek);
                })
                ->where('is_available', true)
                ->orderBy('start_time')
                ->get();
            $this->nextWeek[] = [
                'label' => $day->format('D d M'),
                'date' => $day->toDateString(),
                'entries' => $entries,
            ];
        }
    }

    public function quickSaveSlot(): void
    {
        $this->validate([
            'hospital_name' => ['required','string','max:255'],
            'slot_date' => ['required','date'],
            'start_time' => ['required'],
            'end_time' => ['required','after:start_time'],
            'capacity' => ['required','integer','min:1','max:200'],
        ]);

        \App\Models\DoctorSchedule::create([
            'doctor_id' => $this->doctorId,
            'hospital_name' => $this->hospital_name,
            'date' => $this->slot_date,
            'weekday' => null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'capacity' => $this->capacity,
            'is_available' => true,
            'is_exception' => false,
        ]);

        // reset minimal fields
        $this->start_time = '';
        $this->end_time = '';
        $this->capacity = 25;

        $this->hydrateWeek();
        $this->with();
    }

    public function toggleAvailability(int $id): void
    {
        $s = \App\Models\DoctorSchedule::where('doctor_id', $this->doctorId)->findOrFail($id);
        $s->is_available = ! $s->is_available;
        $s->save();
        $this->hydrateWeek();
    }

    public function deleteSlot(int $id): void
    {
        \App\Models\DoctorSchedule::where('doctor_id', $this->doctorId)->where('id', $id)->delete();
        $this->hydrateWeek();
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Doctor Dashboard</h1>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-zinc-200 overflow-hidden">
                @if($doctor?->profile_photo_path)
                    <img class="w-16 h-16 object-cover" src="{{ Storage::disk('public')->url($doctor->profile_photo_path) }}" alt="Doctor" />
                @endif
            </div>
            <div>
                <div class="text-lg font-semibold">{{ $doctor?->full_name ?? '—' }}</div>
                <div class="text-sm text-zinc-500">{{ $doctor?->specialty ?? '' }}</div>
                <div class="text-xs text-zinc-500">Hospitals: {{ count($doctorHospitals) ? implode(', ', $doctorHospitals) : 'None yet' }}</div>
            </div>
            <div class="ml-auto">
                <flux:link :href="route('doctor.schedules')" wire:navigate>
                    <flux:button variant="primary">Open Calendar</flux:button>
                </flux:link>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Today's Patients</h2>
            <ul class="space-y-2">
                @forelse($todayAppointments as $a)
                    <li class="flex items-center justify-between border-b py-2">
                        <div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}</div>
                            <div class="text-sm text-zinc-500">{{ $a->patient?->name ?? ('Patient #'.$a->patient_id) }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:link :href="route('doctor.appointment', ['appointment' => $a->id])" wire:navigate class="text-teal-600">View Record</flux:link>
                        </div>
                    </li>
                @empty
                    <li class="text-zinc-500">No appointments today.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Upcoming Appointments (7 days)</h2>
            <ul class="space-y-2">
                @forelse($upcoming as $a)
                    <li class="flex items-center justify-between border-b py-2">
                        <div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($a->scheduled_date)->format('Y-m-d') }} {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}</div>
                            <div class="text-sm text-zinc-500">{{ $a->patient?->name ?? ('Patient #'.$a->patient_id) }}</div>
                        </div>
                    </li>
                @empty
                    <li class="text-zinc-500">No upcoming appointments.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Quick Add Slot</h2>
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm block mb-1">Hospital</label>
                    <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="hospital_name">
                        <option value="">Select hospital...</option>
                        @foreach($hospitals as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <flux:input type="date" wire:model="slot_date" label="Date" />
                <flux:input type="time" wire:model="start_time" label="Start" />
                <flux:input type="time" wire:model="end_time" label="End" />
                <flux:input type="number" min="1" max="200" wire:model="capacity" label="Capacity" />
            </div>
            <div class="mt-3">
                <flux:button variant="primary" wire:click="quickSaveSlot">Add Slot</flux:button>
            </div>
            <p class="text-xs text-zinc-500 mt-2">For recurring weekly slots, use the full calendar.</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Your Schedule (This Week)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($nextWeek as $day)
                <div class="border rounded p-2">
                    <div class="text-sm font-medium mb-1">{{ $day['label'] }}</div>
                    <div class="space-y-1">
                        @forelse($day['entries'] as $e)
                            <div class="text-xs flex items-center justify-between">
                                <span>{{ $e->start_time }}-{{ $e->end_time }} • {{ $e->hospital_name }}</span>
                                <span class="ml-2 {{ $e->is_available ? 'text-green-600' : 'text-red-600' }}">{{ $e->is_available ? 'Open' : 'Closed' }}</span>
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                <flux:button size="xs" variant="ghost" wire:click="toggleAvailability({{ $e->id }})">Toggle</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="deleteSlot({{ $e->id }})">Delete</flux:button>
                            </div>
                        @empty
                            <div class="text-xs text-zinc-500">No slots.</div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
