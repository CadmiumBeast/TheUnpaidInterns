<?php

use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

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

    public function mount(Doctor $doctor): void
    {
        $this->doctor = $doctor;
        $this->fill($doctor->only([
            'full_name','specialty','license_number','contact_number','email','schedule_notes','is_active'
        ]));
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
            $this->doctor->user->update(['password' => $this->new_password]);
        }

        $this->redirect(route('admin.doctors.index'), navigate: true);
    }
};
?>

<div class="space-y-6 max-w-2xl">
    <h1 class="text-xl font-semibold">Edit Doctor</h1>

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

        <flux:button type="submit" variant="primary">Save changes</flux:button>
    </form>
</div>
