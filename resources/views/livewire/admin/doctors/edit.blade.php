<?php

use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public Doctor $doctor;

    public string $full_name = '';
    public string $specialty = '';
    public string $license_number = '';
    public string $contact_number = '';
    public string $email = '';
    public string $schedule_notes = '';
    public bool $is_active = true;
    public $profile_photo;
    public string $new_password = '';
    // schedule editor state
    public array $hospitals = [];
    public ?int $weekday = null;
    public string $start_time = '';
    public string $end_time = '';
    public int $capacity = 25;
    public string $hospital_name = '';
    // advanced scheduling
    public string $mode = 'weekly'; // weekly | single | range
    public array $selectedWeekdays = [];
    public ?string $custom_date = null; // Y-m-d
    public ?string $range_start = null; // Y-m-d
    public ?string $range_end = null;   // Y-m-d
    public bool $make_exception = false; // leave/closure

    public function mount(Doctor $doctor): void
    {
        $this->doctor = $doctor;
        $this->fill($doctor->only([
            'full_name','specialty','license_number','contact_number','email','schedule_notes','is_active'
        ]));
    $this->hospitals = config('hospitals.list', []);
    $this->hospital_name = $this->hospitals[0] ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:doctors,license_number,'.$this->doctor->id],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'schedule_notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'new_password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($this->profile_photo) {
            $validated['profile_photo_path'] = $this->profile_photo->store('profile-photos', 'public');
        }

        $this->doctor->update($validated);

        if (!empty($this->new_password) && $this->doctor->user) {
            $this->doctor->user->update(['password' => Hash::make($this->new_password)]);
        }

        $this->redirect(route('admin.doctors.index'), navigate: true);
    }

    public function updatedMode($value = null): void
    {
        // Clear stale validation errors and reset mode-specific fields
        $this->resetErrorBag();
        $this->resetValidation();
        $this->custom_date = null;
        $this->range_start = null;
        $this->range_end = null;
        $this->selectedWeekdays = [];
    }

    public function setMode(string $value): void
    {
        $this->mode = $value;
        $this->updatedMode($value);
    }

    public function addSlot(): void
    {
        $baseRules = [
            'hospital_name' => ['required','string'],
            'start_time' => ['required'],
            'end_time' => ['required','after:start_time'],
        ];
        if (!$this->make_exception) {
            $baseRules['capacity'] = ['required','integer','min:1','max:200'];
        }

        // Mode-specific validation
        if ($this->mode === 'weekly') {
            $baseRules['selectedWeekdays'] = ['required','array','min:1'];
        } elseif ($this->mode === 'single') {
            $baseRules['custom_date'] = ['required','date'];
        } elseif ($this->mode === 'range') {
            $baseRules['range_start'] = ['required','date'];
            $baseRules['range_end'] = ['required','date','after_or_equal:range_start'];
            $baseRules['selectedWeekdays'] = ['required','array','min:1'];
        }

        $this->validate($baseRules);

        $toCreate = [];
        if ($this->mode === 'weekly') {
            foreach ($this->selectedWeekdays as $wd) {
                $toCreate[] = ['weekday' => (int)$wd, 'date' => null];
            }
        } elseif ($this->mode === 'single') {
            $toCreate[] = ['weekday' => null, 'date' => $this->custom_date];
        } else { // range
            $start = \Carbon\Carbon::parse($this->range_start);
            $end = \Carbon\Carbon::parse($this->range_end);
            $days = array_map('intval', $this->selectedWeekdays);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if (in_array($d->dayOfWeek, $days, true)) {
                    $toCreate[] = ['weekday' => null, 'date' => $d->toDateString()];
                }
            }
        }

        $createdCount = 0;
        foreach ($toCreate as $tpl) {
            $query = \App\Models\DoctorSchedule::where('doctor_id', $this->doctor->id)
                ->where('start_time', $this->start_time);
            if ($tpl['date']) {
                $query->whereDate('date', $tpl['date']);
            } else {
                $query->where('weekday', $tpl['weekday']);
            }
            // prevent duplicates regardless of hospital or exception flag
            if ($query->exists()) {
                continue; // skip duplicates silently
            }

            // For available slots, avoid creating cross-hospital duplicates at the same time/weekday
            if (!$this->make_exception && !$tpl['date']) {
                $dupWeekly = \App\Models\DoctorSchedule::where('doctor_id', $this->doctor->id)
                    ->where('start_time', $this->start_time)
                    ->where('weekday', $tpl['weekday'])
                    ->where('is_exception', false)
                    ->exists();
                if ($dupWeekly) {
                    continue;
                }
            }

            \App\Models\DoctorSchedule::create([
                'doctor_id' => $this->doctor->id,
                'hospital_name' => $this->hospital_name ?: ($this->hospitals[0] ?? 'N/A'),
                'date' => $tpl['date'],
                'weekday' => $tpl['weekday'],
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'capacity' => $this->make_exception ? 0 : $this->capacity,
                'is_available' => $this->make_exception ? false : true,
                'is_exception' => $this->make_exception,
            ]);
            $createdCount++;
        }

        // reset some inputs
        $this->start_time = $this->end_time = '';
        if (!$this->make_exception) { $this->capacity = 25; }
    }

    public function toggleSlot(int $id): void
    {
        $s = \App\Models\DoctorSchedule::where('doctor_id', $this->doctor->id)->findOrFail($id);
        $s->is_available = ! $s->is_available;
        $s->save();
    }

    public function deleteSlot(int $id): void
    {
        \App\Models\DoctorSchedule::where('doctor_id', $this->doctor->id)->where('id', $id)->delete();
    }
};
?>

<div class="space-y-6">
    <h1 class="text-xl font-semibold">Edit Doctor</h1>

    <form wire:submit="save" class="space-y-4 max-w-2xl">
        <flux:input wire:model="full_name" label="Full name" required />
        <div>
            <label class="text-sm block mb-1">Specialty</label>
            <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="specialty" required>
                <option value="">Select specialty...</option>
                @foreach(config('specialties.list', []) as $spec)
                    <option value="{{ $spec }}">{{ $spec }}</option>
                @endforeach
            </select>
        </div>
        <flux:input wire:model="license_number" label="License number" required />
        <flux:input wire:model="contact_number" label="Contact number" />
        <flux:input wire:model="email" label="Email" type="email" />
        <flux:textarea wire:model="schedule_notes" label="Schedule notes" />
        <div>
            <label class="text-sm font-medium">Profile photo</label>
            <input type="file" wire:model="profile_photo" class="block mt-1" />
        </div>
        <div class="flex items-center gap-2">
            <flux:switch wire:model="is_active" />
            <span>Active</span>
        </div>

        <flux:button type="submit" variant="primary">Save changes</flux:button>
    </form>

    <div class="mt-8">
        <h2 class="text-lg font-medium mb-3">Manage Slots</h2>
        <div class="grid md:grid-cols-4 gap-3 max-w-3xl">
            <div>
                <label class="text-sm block mb-1">Hospital</label>
                <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.defer="hospital_name">
                    @foreach($hospitals as $h)
                        <option value="{{ $h }}">{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex items-end gap-4">
                <div>
                    <label class="text-sm block mb-1">Mode</label>
                    <select class="border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.live="mode" wire:change="setMode($event.target.value)">
                        <option value="weekly">Weekly (by weekdays)</option>
                        <option value="single">Single date</option>
                        <option value="range">Date range</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input id="make_exception" type="checkbox" wire:model="make_exception" class="border rounded" />
                    <label for="make_exception" class="text-sm">Mark as leave/closure</label>
                </div>
            </div>
            <div class="md:col-span-4" wire:key="slot-mode-{{ $mode }}">
                @if($mode === 'weekly')
                    <div class="flex flex-wrap gap-3">
                        @php $days = [['0','Sun'],['1','Mon'],['2','Tue'],['3','Wed'],['4','Thu'],['5','Fri'],['6','Sat']]; @endphp
                        @foreach($days as [$val,$lbl])
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" value="{{ $val }}" wire:model="selectedWeekdays" /> {{ $lbl }}
                        </label>
                        @endforeach
                    </div>
                @elseif($mode === 'single')
                    <div>
                        <label class="text-sm block mb-1">Select Date</label>
                        <input type="date" class="border rounded-md p-2 w-full" wire:model="custom_date" />
                    </div>
                @else
                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-sm block mb-1">From</label>
                            <input type="date" class="border rounded-md p-2 w-full" wire:model="range_start" />
                        </div>
                        <div>
                            <label class="text-sm block mb-1">To</label>
                            <input type="date" class="border rounded-md p-2 w-full" wire:model="range_end" />
                        </div>
                        <div>
                            <label class="text-sm block mb-1">Weekdays in Range</label>
                            <div class="flex flex-wrap gap-3">
                                @php $days = [['0','Sun'],['1','Mon'],['2','Tue'],['3','Wed'],['4','Thu'],['5','Fri'],['6','Sat']]; @endphp
                                @foreach($days as [$val,$lbl])
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" value="{{ $val }}" wire:model="selectedWeekdays" /> {{ $lbl }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <flux:input type="time" wire:model.live="start_time" label="Start" />
            <flux:input type="time" wire:model.live="end_time" label="End" />
            <div>
                <label class="text-sm block mb-1">Capacity</label>
                <input type="number" min="1" max="200" class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="capacity" @disabled($make_exception) />
            </div>
            <div class="md:col-span-4">
                <flux:button type="button" variant="primary" wire:click="addSlot">Add Slot</flux:button>
            </div>
        </div>

        <div class="mt-4 grid md:grid-cols-2 gap-3">
            @foreach(\App\Models\DoctorSchedule::where('doctor_id', $doctor->id)->orderByRaw('COALESCE(weekday, 7), start_time')->get() as $s)
                <div class="border rounded p-3">
                    <div class="text-sm font-medium">{{ $s->hospital_name }} —
                        @if($s->date)
                            {{ \Carbon\Carbon::parse($s->date)->format('D, d M Y') }}
                        @else
                            {{ ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$s->weekday ?? 0] }}
                        @endif
                    </div>
                    <div class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
                        @if($s->is_exception)
                            • leave/closure
                        @else
                            • cap {{ $s->capacity ?? 25 }}
                        @endif
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        @if(!$s->is_exception)
                        <flux:button size="xs" variant="ghost" wire:click="toggleSlot({{ $s->id }})">{{ $s->is_available ? 'Close' : 'Open' }}</flux:button>
                        @endif
                        <flux:button size="xs" variant="danger" wire:click="deleteSlot({{ $s->id }})">Delete</flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
