<?php

use App\Models\Doctor;
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

    public function mount(): void
    {
        $this->is_active = true;
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

        $this->redirect(route('admin.doctors.index'), navigate: true);
    }
};
?>

<div class="space-y-6 max-w-2xl">
    <h1 class="text-xl font-semibold">Add Doctor</h1>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="full_name" label="Full name" required />
        <flux:input wire:model="specialty" label="Specialty" required />
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

        <flux:button type="submit" variant="primary">Save</flux:button>
    </form>
</div>
