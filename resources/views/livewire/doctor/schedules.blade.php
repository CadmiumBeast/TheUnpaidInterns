<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public array $week = [];
    public string $view = 'week'; // week|month
    public ?int $doctorId = null;
    public string $date = '';

    // Form state
    public string $hospital_name = '';
    public array $hospitals = [];
    public ?int $weekday = null;
    public string $start_time = '';
    public string $end_time = '';
    public array $breaks = [];
    public string $recurrence_rule = '';
    public bool $is_exception = false;
    public bool $is_available = true;

    public ?int $editingId = null;

    public function mount(): void
    {
        $user = auth()->user();
        $doctor = Doctor::where('user_id', $user->id)->first();
        $this->doctorId = $doctor?->id;
        $this->date = now()->toDateString();
    $this->hospitals = config('hospitals.list', []);
        $this->hydrateWeek();
    }

    public function hydrateWeek(): void
    {
        $start = Carbon::parse($this->date)->startOfWeek();
        $this->week = [];
        for ($i = 0; $i < 7; $i++) {
            $day = (clone $start)->addDays($i);
            $this->week[] = [
                'label' => $day->format('D d M'),
                'date' => $day->toDateString(),
                'entries' => DoctorSchedule::where('doctor_id', $this->doctorId)
                    ->where(function($q) use ($day) {
                        $q->whereDate('date', $day->toDateString())
                          ->orWhere('weekday', $day->dayOfWeek);
                    })
                    ->orderBy('start_time')->get(),
            ];
        }
    }

    public function prev(): void
    {
        $this->date = Carbon::parse($this->date)->subWeek()->toDateString();
        $this->hydrateWeek();
    }

    public function next(): void
    {
        $this->date = Carbon::parse($this->date)->addWeek()->toDateString();
        $this->hydrateWeek();
    }

    public function edit(int $id): void
    {
        $s = DoctorSchedule::findOrFail($id);
        $this->editingId = $s->id;
        $this->hospital_name = $s->hospital_name;
        $this->weekday = $s->weekday;
        $this->start_time = $s->start_time;
        $this->end_time = $s->end_time;
        $this->breaks = $s->breaks ?? [];
        $this->recurrence_rule = $s->recurrence_rule ?? '';
        $this->is_exception = $s->is_exception;
        $this->is_available = $s->is_available;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->hospital_name = '';
        $this->weekday = null;
        $this->start_time = '';
        $this->end_time = '';
        $this->breaks = [];
        $this->recurrence_rule = '';
        $this->is_exception = false;
        $this->is_available = true;
    }

    public function save(): void
    {
        $this->validate([
            'hospital_name' => ['required','string','max:255'],
            'start_time' => ['required'],
            'end_time' => ['required','after:start_time'],
            'weekday' => ['nullable','integer','between:0,6'],
        ]);

        $schedule = DoctorSchedule::updateOrCreate(
            ['id' => $this->editingId],
            [
                'doctor_id' => $this->doctorId,
                'hospital_name' => $this->hospital_name,
                'weekday' => $this->weekday,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'breaks' => $this->breaks,
                'recurrence_rule' => $this->recurrence_rule,
                'is_exception' => $this->is_exception,
                'is_available' => $this->is_available,
            ]
        );

        // Basic notification to the doctor email (if exists)
        $doctor = Doctor::find($this->doctorId);
        if ($doctor && $doctor->email) {
            try {
                Mail::raw(
                    'Your schedule has been '.($this->editingId ? 'updated' : 'created')." for {$this->hospital_name} {$this->start_time}-{$this->end_time}",
                    function($m) use ($doctor) {
                        $m->to($doctor->email)->subject('Schedule update');
                    }
                );
            } catch (\Throwable $e) {
                // Silent fail; future enhancement: queue + notifications
            }
        }

        $this->resetForm();
        $this->hydrateWeek();
    }

    public function delete(int $id): void
    {
        DoctorSchedule::where('doctor_id', $this->doctorId)->where('id', $id)->delete();
        $this->hydrateWeek();
    }

    public function toggleAvailability(int $id): void
    {
        $s = DoctorSchedule::where('doctor_id', $this->doctorId)->findOrFail($id);
        $s->is_available = ! $s->is_available;
        $s->save();
        $this->hydrateWeek();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">My Schedule</h1>
        <div class="space-x-2">
            <flux:button wire:click="prev">Prev</flux:button>
            <flux:button wire:click="next">Next</flux:button>
        </div>
    </div>

    <div class="grid md:grid-cols-7 gap-4">
        @foreach($week as $day)
            <div class="bg-white dark:bg-zinc-900 rounded-lg p-3 shadow">
                <div class="font-medium mb-2">{{ $day['label'] }}</div>
                <div class="space-y-2">
                    @foreach($day['entries'] as $e)
                        <div class="border rounded p-2">
                            <div class="text-sm">{{ $e->hospital_name }}</div>
                            <div class="text-xs text-zinc-500">{{ $e->start_time }} - {{ $e->end_time }}</div>
                            <div class="text-xs">{{ $e->is_available ? 'Available' : 'Unavailable' }}</div>
                            <div class="flex items-center gap-1 mt-2">
                                <flux:button size="xs" wire:click="edit({{ $e->id }})">Edit</flux:button>
                                <flux:button size="xs" wire:click="toggleAvailability({{ $e->id }})">Toggle</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="delete({{ $e->id }})">Delete</flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-3">Add / Edit Slot</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm block mb-1">Hospital</label>
                <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="hospital_name">
                    <option value="">Select hospital...</option>
                    @foreach($hospitals as $h)
                        <option value="{{ $h }}">{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <flux:select wire:model="weekday" label="Weekday">
                <option value="">—</option>
                <option value="1">Mon</option>
                <option value="2">Tue</option>
                <option value="3">Wed</option>
                <option value="4">Thu</option>
                <option value="5">Fri</option>
                <option value="6">Sat</option>
                <option value="0">Sun</option>
            </flux:select>
            <div class="grid grid-cols-2 gap-2">
                <flux:input type="time" wire:model="start_time" label="Start" />
                <flux:input type="time" wire:model="end_time" label="End" />
            </div>
            <flux:input wire:model="recurrence_rule" label="Recurrence (RRULE)" placeholder="e.g. FREQ=WEEKLY;BYDAY=MO,WE,FR" />
            <div class="flex items-center gap-2">
                <flux:switch wire:model="is_exception" />
                <span>Exception (holiday/clinic)</span>
            </div>
            <div class="flex items-center gap-2">
                <flux:switch wire:model="is_available" />
                <span>Available</span>
            </div>
        </div>
        <div class="mt-3">
            <flux:button variant="primary" wire:click="save">Save</flux:button>
            <flux:button variant="ghost" wire:click="resetForm">Reset</flux:button>
        </div>
    </div>
</div>
