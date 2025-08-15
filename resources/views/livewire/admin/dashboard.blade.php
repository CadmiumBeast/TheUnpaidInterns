<?php

use App\Models\Doctor;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with(): array
    {
        return [
            'doctorCount' => Doctor::count(),
            'activeDoctors' => Doctor::where('is_active', true)->count(),
            'userCount' => User::count(),
        ];
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Admin Dashboard</h1>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="p-4 rounded-lg bg-teal-50">
            <div class="text-sm text-gray-600">Doctors</div>
            <div class="text-3xl font-bold">{{ $doctorCount }}</div>
        </div>
        <div class="p-4 rounded-lg bg-green-50">
            <div class="text-sm text-gray-600">Active Doctors</div>
            <div class="text-3xl font-bold">{{ $activeDoctors }}</div>
        </div>
        <div class="p-4 rounded-lg bg-blue-50">
            <div class="text-sm text-gray-600">Total Users</div>
            <div class="text-3xl font-bold">{{ $userCount }}</div>
        </div>
    </div>

    <div class="flex gap-3">
        <flux:link :href="route('admin.doctors.index')" wire:navigate>
            <flux:button variant="primary">Manage Doctors</flux:button>
        </flux:link>
        <flux:link :href="route('admin.schedules')" wire:navigate>
            <flux:button>Manage Schedules</flux:button>
        </flux:link>
        <flux:link :href="route('admin.appointments')" wire:navigate>
            <flux:button>Manage Appointments</flux:button>
        </flux:link>
    </div>
</div>
