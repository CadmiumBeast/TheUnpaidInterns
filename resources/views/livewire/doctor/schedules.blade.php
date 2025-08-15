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
    public array $monthGrid = [];

    // Form state
    public string $hospital_name = '';
    public array $hospitals = [];
    public ?int $weekday = null;
    public string $start_time = '';
    public string $end_time = '';
    public int $capacity = 25;
    public array $breaks = [];
    public string $recurrence_rule = '';
    public array $recurrence_days = [];
    public bool $is_exception = false;
    public bool $is_available = true;

    public ?int $editingId = null;

    // Absence/leave form
    public string $absence_from = '';
    public string $absence_to = '';

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

    public function hydrateMonth(): void
    {
        $start = Carbon::parse($this->date)->startOfMonth()->startOfWeek();
        $end = Carbon::parse($this->date)->endOfMonth()->endOfWeek();
        $cursor = $start->copy();
        $this->monthGrid = [];
        while ($cursor->lte($end)) {
            $weekRow = [];
            for ($i=0; $i<7; $i++) {
                $day = $cursor->copy();
                $entries = DoctorSchedule::where('doctor_id', $this->doctorId)
                    ->where(function($q) use ($day) {
                        $q->whereDate('date', $day->toDateString())
                          ->orWhere('weekday', $day->dayOfWeek);
                    })
                    ->orderBy('start_time')->get();
                $weekRow[] = [
                    'label' => $day->format('j'),
                    'date' => $day->toDateString(),
                    'inMonth' => $day->isSameMonth(Carbon::parse($this->date)),
                    'entries' => $entries,
                ];
                $cursor->addDay();
            }
            $this->monthGrid[] = $weekRow;
        }
    }

    public function prev(): void
    {
        if ($this->view === 'month') {
            $this->date = Carbon::parse($this->date)->subMonth()->toDateString();
            $this->hydrateMonth();
        } else {
            $this->date = Carbon::parse($this->date)->subWeek()->toDateString();
            $this->hydrateWeek();
        }
    }

    public function next(): void
    {
        if ($this->view === 'month') {
            $this->date = Carbon::parse($this->date)->addMonth()->toDateString();
            $this->hydrateMonth();
        } else {
            $this->date = Carbon::parse($this->date)->addWeek()->toDateString();
            $this->hydrateWeek();
        }
    }

    public function setView(string $view): void
    {
        $this->view = $view;
        if ($view === 'month') $this->hydrateMonth(); else $this->hydrateWeek();
    }

    public function edit(int $id): void
    {
        $s = DoctorSchedule::findOrFail($id);
        $this->editingId = $s->id;
        $this->hospital_name = $s->hospital_name;
        $this->weekday = $s->weekday;
        $this->start_time = $s->start_time;
        $this->end_time = $s->end_time;
    $this->capacity = $s->capacity ?? 25;
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
    $this->capacity = 25;
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
            'capacity' => ['required','integer','min:1','max:200'],
        ]);

        // Build RRULE from weekly days if provided and no explicit recurrence_rule typed
        if (empty($this->recurrence_rule) && !empty(array_filter($this->recurrence_days))) {
            $map = ['SU','MO','TU','WE','TH','FR','SA'];
            $days = [];
            foreach ($this->recurrence_days as $i => $val) {
                if ($val) { $days[] = $map[(int)$i]; }
            }
            if ($days) {
                $this->recurrence_rule = 'FREQ=WEEKLY;BYDAY=' . implode(',', $days);
            }
        }

        $schedule = DoctorSchedule::updateOrCreate(
            ['id' => $this->editingId],
            [
                'doctor_id' => $this->doctorId,
                'hospital_name' => $this->hospital_name,
                'weekday' => $this->weekday,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'capacity' => $this->capacity,
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

    public function markUnavailableRange(): void
    {
        $this->validate([
            'absence_from' => ['required','date'],
            'absence_to' => ['required','date','after_or_equal:absence_from'],
        ]);

        $from = Carbon::parse($this->absence_from);
        $to = Carbon::parse($this->absence_to);
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            DoctorSchedule::create([
                'doctor_id' => $this->doctorId,
                'hospital_name' => 'N/A',
                'date' => $cursor->toDateString(),
                'weekday' => null,
                'start_time' => '00:00',
                'end_time' => '23:59',
                'is_exception' => true,
                'is_available' => false,
            ]);
            $cursor->addDay();
        }
        $this->hydrateWeek();
        $this->hydrateMonth();
        $this->absence_from = $this->absence_to = '';
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">My Schedule</h1>
        <div class="space-x-2">
            <flux:button variant="ghost" :class="$view==='week' ? 'font-semibold' : ''" wire:click="setView('week')">Week</flux:button>
            <flux:button variant="ghost" :class="$view==='month' ? 'font-semibold' : ''" wire:click="setView('month')">Month</flux:button>
            <flux:button wire:click="prev">Prev</flux:button>
            <flux:button wire:click="next">Next</flux:button>
        </div>
    </div>

    @if($view==='week')
    <div class="grid md:grid-cols-7 gap-4">
        @foreach($week as $day)
            <div class="bg-white dark:bg-zinc-900 rounded-lg p-3 shadow">
                <div class="font-medium mb-2">{{ $day['label'] }}</div>
                <div class="space-y-2">
                    @foreach($day['entries'] as $e)
                        <div class="border rounded p-2">
                            <div class="text-sm">{{ $e->hospital_name }}</div>
                            <div class="text-xs text-zinc-500">{{ $e->start_time }} - {{ $e->end_time }}</div>
                            <?php $booked = \App\Models\Appointment::where('doctor_id', $e->doctor_id)->whereDate('scheduled_date', $day['date'])->where('start_time', $e->start_time)->count(); ?>
                            <div class="text-xs">{{ $booked }}/{{ $e->capacity ?? 25 }} booked</div>
                            <div class="text-xs">{{ $e->is_available ? 'Available' : ($e->is_exception ? 'Unavailable (Leave)' : 'Unavailable') }}</div>
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
    @else
    <div class="grid grid-cols-7 gap-2">
        @foreach($monthGrid as $row)
            @foreach($row as $cell)
                <div class="border rounded p-2 {{ $cell['inMonth'] ? '' : 'opacity-50' }}">
                    <div class="text-xs font-medium flex items-center justify-between">
                        <span>{{ $cell['label'] }}</span>
                        <flux:button size="xs" variant="ghost" wire:click="$set('date','{{ $cell['date'] }}'); setView('week')">+</flux:button>
                    </div>
                    <div class="mt-1 space-y-1">
                        @foreach($cell['entries'] as $e)
                            <div class="text-[11px] truncate">
                                {{ $e->start_time }} {{ $e->hospital_name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
    @endif

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
            <flux:input type="number" min="1" max="200" wire:model="capacity" label="Capacity (per slot)" />
            <flux:input wire:model="recurrence_rule" label="Recurrence (RRULE)" placeholder="e.g. FREQ=WEEKLY;BYDAY=MO,WE,FR" />
            <div>
                <label class="text-sm block mb-1">Weekly days</label>
                <div class="grid grid-cols-7 gap-1 text-xs">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $i=>$d)
                        <label class="flex items-center gap-1"><input type="checkbox" wire:model.defer="recurrence_days.{{ $i }}" value="1" /> {{ $d }}</label>
                    @endforeach
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Optional: pick days to auto-set RRULE (FREQ=WEEKLY)</p>
            </div>
            <div>
                <label class="text-sm block mb-1">Breaks</label>
                <div class="space-y-1">
                    @foreach(($breaks ?? []) as $idx => $br)
                        <div class="grid grid-cols-2 gap-1">
                            <input type="time" class="border rounded p-1 bg-white dark:bg-zinc-900" wire:model="breaks.{{ $idx }}.start" />
                            <input type="time" class="border rounded p-1 bg-white dark:bg-zinc-900" wire:model="breaks.{{ $idx }}.end" />
                        </div>
                    @endforeach
                </div>
                <div class="mt-1 flex gap-2">
                    <flux:button type="button" size="xs" wire:click="$push('breaks', {start: '', end: ''})">+ Add break</flux:button>
                    <flux:button type="button" size="xs" variant="ghost" wire:click="$set('breaks', [])">Clear breaks</flux:button>
                </div>
            </div>
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

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-3">Mark Unavailable (Leave)</h2>
        <div class="grid md:grid-cols-4 gap-3 items-end">
            <flux:input type="date" wire:model="absence_from" label="From" />
            <flux:input type="date" wire:model="absence_to" label="To" />
            <div class="md:col-span-2">
                <flux:button variant="primary" wire:click="markUnavailableRange">Mark Unavailable</flux:button>
            </div>
        </div>
        <p class="text-xs text-zinc-500 mt-2">Creates all-day exception entries for selected dates and hides availability.</p>
    </div>
</div>
