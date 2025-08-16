<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public string $full_name = '';
    public string $specialty = '';
    public string $license_number = '';
    public string $contact_number = '';
    public string $email = '';
    public string $schedule_notes = '';
    public bool $is_active = true;
    public $profile_photo;

    // Optional: create linked user account
    public bool $create_user = true;
    public string $username = '';
    public string $password = '';

    // Optional: create initial schedules
    public array $hospitals = [];
    public array $initialSchedules = [
        // ['hospital_name' => '', 'weekday' => null, 'start_time' => '', 'end_time' => '']
    ];
    // Quick add state
    public string $qa_hospital_name = '';
    public string $qa_mode = 'weekly'; // weekly | single | range
    public array $qa_selectedWeekdays = [];
    public ?string $qa_custom_date = null;
    public ?string $qa_range_start = null;
    public ?string $qa_range_end = null;
    public string $qa_start_time = '';
    public string $qa_end_time = '';
    public int $qa_capacity = 25;
    public bool $qa_exception = false;

    public function updatedQaMode($value = null): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->qa_custom_date = null;
        $this->qa_range_start = null;
        $this->qa_range_end = null;
        $this->qa_selectedWeekdays = [];
    }

    public function setQaMode(string $value): void
    {
        $this->qa_mode = $value;
        $this->updatedQaMode($value);
    }

    public function mount(): void
    {
        $this->is_active = true;
        $this->hospitals = config('hospitals.list', []);
    $this->qa_hospital_name = $this->hospitals[0] ?? '';
    }

    public function addInitialSlot(): void
    {
        $this->initialSchedules[] = [
            'hospital_name' => '',
            'weekday' => '',
            'start_time' => '',
            'end_time' => '',
            'capacity' => 25,
        ];
    }

    public function removeInitialSlot(int $index): void
    {
        if (isset($this->initialSchedules[$index])) {
            unset($this->initialSchedules[$index]);
            $this->initialSchedules = array_values($this->initialSchedules);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', Rule::unique('doctors', 'license_number')],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'schedule_notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'create_user' => ['boolean'],
            'username' => [$this->create_user ? 'required' : 'nullable', 'string', 'max:255'],
            'password' => [$this->create_user ? 'required' : 'nullable', 'string', 'min:8'],
        ]);

        $userId = null;
        if ($this->create_user) {
            $user = User::create([
                'name' => $this->username,
                'email' => $this->email ?? ($this->username.'@example.com'),
                'password' => Hash::make($this->password),
                'type' => 'doctor',
            ]);
            $userId = $user->id;
        }

        $photoPath = null;
        if ($this->profile_photo) {
            $photoPath = $this->profile_photo->store('profile-photos', 'public');
        }

    $doctor = Doctor::create([
            'user_id' => $userId,
            'full_name' => $this->full_name,
            'specialty' => $this->specialty,
            'license_number' => $this->license_number,
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'schedule_notes' => $this->schedule_notes,
            'is_active' => $this->is_active,
            'profile_photo_path' => $photoPath,
        ]);

        // Create initial schedules if provided
        foreach ($this->initialSchedules as $slot) {
            if (!empty($slot['hospital_name']) && !empty($slot['start_time']) && !empty($slot['end_time'])) {
                // Prevent duplicate time for same doctor across different hospitals in same day-of-week/date
                $conflict = \App\Models\DoctorSchedule::where('doctor_id', $doctor->id)
                    ->where('start_time', $slot['start_time'])
                    ->when(($slot['weekday'] ?? '') !== '', fn($q) => $q->where('weekday', (int)$slot['weekday']))
                    ->when(($slot['weekday'] ?? '') === '' && !empty($slot['date'] ?? null), fn($q) => $q->whereDate('date', $slot['date']))
                    ->where('is_exception', false)
                    ->exists();
                if ($conflict) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'initialSchedules' => ['Conflicting slot: a slot at this time already exists for this doctor.'],
                    ]);
                }

                // Prevent scheduling during absences/exceptions
                $absenceConflict = \App\Models\DoctorSchedule::where('doctor_id', $doctor->id)
                    ->where('is_exception', true)
                    ->when(($slot['weekday'] ?? '') !== '', fn($q) => $q->where('weekday', (int)$slot['weekday']))
                    ->when(($slot['weekday'] ?? '') === '' && !empty($slot['date'] ?? null), fn($q) => $q->whereDate('date', $slot['date']))
                    ->exists();
                if ($absenceConflict) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'initialSchedules' => ['This time falls within an absence/leave period.'],
                    ]);
                }
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'hospital_name' => $slot['hospital_name'],
                    'date' => $slot['date'] ?? null,
                    'weekday' => $slot['weekday'] !== '' ? (int)$slot['weekday'] : null,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
            'capacity' => isset($slot['capacity']) && (int)$slot['capacity'] > 0 ? (int)$slot['capacity'] : 25,
                    'is_available' => true,
                    'is_exception' => false,
                ]);
            }
        }

        $this->redirect(route('admin.doctors.index'), navigate: true);
    }
    
    public function addQuickSlot(): void
    {
        $rules = [
            'qa_hospital_name' => ['required','string'],
            'qa_start_time' => ['required'],
            'qa_end_time' => ['required','after:qa_start_time'],
        ];
        if (!$this->qa_exception) {
            $rules['qa_capacity'] = ['required','integer','min:1','max:200'];
        }
        if ($this->qa_mode === 'weekly') {
            $rules['qa_selectedWeekdays'] = ['required','array','min:1'];
        } elseif ($this->qa_mode === 'single') {
            $rules['qa_custom_date'] = ['required','date'];
        } elseif ($this->qa_mode === 'range') {
            $rules['qa_range_start'] = ['required','date'];
            $rules['qa_range_end'] = ['required','date','after_or_equal:qa_range_start'];
            $rules['qa_selectedWeekdays'] = ['required','array','min:1'];
        }
        $this->validate($rules);

        $toAdd = [];
        if ($this->qa_mode === 'weekly') {
            foreach ($this->qa_selectedWeekdays as $wd) {
                $toAdd[] = [
                    'hospital_name' => $this->qa_hospital_name,
                    'weekday' => (int)$wd,
                    'date' => null,
                    'start_time' => $this->qa_start_time,
                    'end_time' => $this->qa_end_time,
                    'capacity' => $this->qa_exception ? 0 : $this->qa_capacity,
                    'is_exception' => $this->qa_exception,
                ];
            }
        } elseif ($this->qa_mode === 'single') {
            $toAdd[] = [
                'hospital_name' => $this->qa_hospital_name,
                'weekday' => null,
                'date' => $this->qa_custom_date,
                'start_time' => $this->qa_start_time,
                'end_time' => $this->qa_end_time,
                'capacity' => $this->qa_exception ? 0 : $this->qa_capacity,
                'is_exception' => $this->qa_exception,
            ];
        } else {
            $start = \Carbon\Carbon::parse($this->qa_range_start);
            $end = \Carbon\Carbon::parse($this->qa_range_end);
            $days = array_map('intval', $this->qa_selectedWeekdays);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if (in_array($d->dayOfWeek, $days, true)) {
                    $toAdd[] = [
                        'hospital_name' => $this->qa_hospital_name,
                        'weekday' => null,
                        'date' => $d->toDateString(),
                        'start_time' => $this->qa_start_time,
                        'end_time' => $this->qa_end_time,
                        'capacity' => $this->qa_exception ? 0 : $this->qa_capacity,
                        'is_exception' => $this->qa_exception,
                    ];
                }
            }
        }

        foreach ($toAdd as $slot) {
            $exists = collect($this->initialSchedules)->first(fn($s) => ($s['date'] ?? null) === ($slot['date'] ?? null) && ($s['weekday'] ?? null) === ($slot['weekday'] ?? null) && $s['start_time'] === $slot['start_time']);
            if (!$exists) {
                $this->initialSchedules[] = $slot;
            }
        }
    }
};
?>

<div class="space-y-6 max-w-2xl">
    <h1 class="text-xl font-semibold">Add Doctor</h1>
        <form wire:submit.prevent="save" class="space-y-4">
            @if ($errors->any())
                <div class="p-2 rounded text-sm bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    <form wire:submit="save" class="space-y-4">
            @error('full_name') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
        <flux:input wire:model="full_name" label="Full name" required />
            @error('specialty') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
        <div>
            <label class="text-sm block mb-1">Specialty</label>
            <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="specialty" required>
                <option value="">Select specialty...</option>
                @foreach(config('specialties.list', []) as $spec)
                    <option value="{{ $spec }}">{{ $spec }}</option>
                @endforeach
            </select>
        </div>
            @error('license_number') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
        <flux:input wire:model="license_number" label="License number" required />
        <flux:input wire:model="contact_number" label="Contact number" />
        <flux:input wire:model="email" label="Email" type="email" />
        <flux:textarea wire:model="schedule_notes" label="Schedule notes" />
        <div>
            <label class="text-sm font-medium">Profile photo</label>
                @if ($profile_photo)
                    <div class="mt-2">
                        <img src="{{ $profile_photo->temporaryUrl() }}" alt="Preview" class="h-16 w-16 rounded object-cover" />
                    </div>
                @endif
                @error('profile_photo') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            <input type="file" wire:model="profile_photo" class="block mt-1" />
        </div>
        <div class="flex items-center gap-2">
            <flux:switch wire:model="is_active" />
            <span>Active</span>
        </div>

        <div class="border-t pt-4 space-y-2">
            <div class="flex items-center gap-2">
                <flux:switch wire:model="create_user" />
                <span>Create login for this doctor</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="create_user">
                <flux:input wire:model="username" label="Username / Display name" />
                <flux:input wire:model="password" label="Temporary password" type="password" />
            </div>
        </div>

        <div class="border-t pt-4 space-y-3">
            <h2 class="text-lg font-medium">Initial Schedule (optional)</h2>
            <p class="text-sm text-zinc-500">Add weekly, single-date, or date-range slots to get this doctor visible in appointments.</p>
            <div class="grid md:grid-cols-4 gap-3 max-w-3xl">
                <div>
                    <label class="text-sm block mb-1">Hospital</label>
                    <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.defer="qa_hospital_name">
                        @foreach($hospitals as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex items-end gap-4">
                    <div>
                        <label class="text-sm block mb-1">Mode</label>
                        <select class="border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.live="qa_mode" wire:change="setQaMode($event.target.value)">
                            <option value="weekly">Weekly</option>
                            <option value="single">Single date</option>
                            <option value="range">Date range</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="qa_exception" type="checkbox" wire:model="qa_exception" class="border rounded" />
                        <label for="qa_exception" class="text-sm">Mark as leave/closure</label>
                    </div>
                </div>
                <div class="md:col-span-4" wire:key="qa-mode-{{ $qa_mode }}">
                    @if($qa_mode === 'weekly')
                        <div class="flex flex-wrap gap-3">
                            @php $days = [['0','Sun'],['1','Mon'],['2','Tue'],['3','Wed'],['4','Thu'],['5','Fri'],['6','Sat']]; @endphp
                            @foreach($days as [$val,$lbl])
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" value="{{ $val }}" wire:model="qa_selectedWeekdays" /> {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                    @elseif($qa_mode === 'single')
                        <div>
                            <label class="text-sm block mb-1">Select Date</label>
                            <input type="date" class="border rounded-md p-2 w-full" wire:model="qa_custom_date" />
                        </div>
                    @else
                        <div class="grid md:grid-cols-3 gap-3">
                            <div>
                                <label class="text-sm block mb-1">From</label>
                                <input type="date" class="border rounded-md p-2 w-full" wire:model="qa_range_start" />
                            </div>
                            <div>
                                <label class="text-sm block mb-1">To</label>
                                <input type="date" class="border rounded-md p-2 w-full" wire:model="qa_range_end" />
                            </div>
                            <div>
                                <label class="text-sm block mb-1">Weekdays in Range</label>
                                <div class="flex flex-wrap gap-3">
                                    @php $days = [['0','Sun'],['1','Mon'],['2','Tue'],['3','Wed'],['4','Thu'],['5','Fri'],['6','Sat']]; @endphp
                                    @foreach($days as [$val,$lbl])
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" value="{{ $val }}" wire:model="qa_selectedWeekdays" /> {{ $lbl }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="text-sm block mb-1">Start</label>
                    <input type="time" class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.live="qa_start_time" />
                </div>
                <div>
                    <label class="text-sm block mb-1">End</label>
                    <input type="time" class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model.live="qa_end_time" />
                </div>
                <div>
                    <label class="text-sm block mb-1">Capacity</label>
                    <input type="number" min="1" max="200" class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="qa_capacity" @disabled($qa_exception) />
                </div>
                <div class="md:col-span-4">
                    <flux:button type="button" variant="primary" wire:click="addQuickSlot">Add Slot</flux:button>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($initialSchedules as $idx => $s)
                <div class="grid md:grid-cols-5 gap-2 items-end">
                    <div>
                        <label class="text-sm block mb-1">Hospital</label>
                        <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="initialSchedules.{{ $idx }}.hospital_name">
                            <option value="">Select...</option>
                            @foreach($hospitals as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm block mb-1">Weekday (or leave empty and set a Date below)</label>
                        <select class="w-full border rounded-md p-2 bg-white dark:bg-zinc-900" wire:model="initialSchedules.{{ $idx }}.weekday">
                            <option value="">—</option>
                            <option value="1">Mon</option>
                            <option value="2">Tue</option>
                            <option value="3">Wed</option>
                            <option value="4">Thu</option>
                            <option value="5">Fri</option>
                            <option value="6">Sat</option>
                            <option value="0">Sun</option>
                        </select>
                        <div class="mt-2">
                            <label class="text-xs block mb-1">Date (optional for single date)</label>
                            <input type="date" class="w-full border rounded-md p-2" wire:model="initialSchedules.{{ $idx }}.date" />
                        </div>
                    </div>
                    <flux:input type="time" wire:model="initialSchedules.{{ $idx }}.start_time" label="Start" />
                    <flux:input type="time" wire:model="initialSchedules.{{ $idx }}.end_time" label="End" />
            <flux:input type="number" min="1" max="200" wire:model="initialSchedules.{{ $idx }}.capacity" label="Capacity" />
                    <div class="md:col-span-5">
                        <flux:button type="button" wire:click="removeInitialSlot({{ $idx }})">Remove</flux:button>
                    </div>
                </div>
                @endforeach
            </div>
        <flux:button type="button" wire:click="addInitialSlot">+ Add Slot</flux:button>
        </div>

        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
