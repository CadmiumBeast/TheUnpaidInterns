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

    public function mount(): void
    {
        $this->is_active = true;
        $this->hospitals = config('hospitals.list', []);
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
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'hospital_name' => $slot['hospital_name'],
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
        <flux:input wire:model="specialty" label="Specialty" required />
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
            <h2 class="text-lg font-medium">Initial Weekly Schedule (optional)</h2>
            <p class="text-sm text-zinc-500">Add one or more recurring slots to get this doctor visible in appointments.</p>
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
                        <label class="text-sm block mb-1">Weekday</label>
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
