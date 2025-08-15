<?php

use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $search = '';

    public function with(): array
    {
        $query = Doctor::query()
            ->when($this->search, fn($q) => $q->where('full_name', 'like', "%{$this->search}%")
                ->orWhere('specialty', 'like', "%{$this->search}%")
                ->orWhere('license_number', 'like', "%{$this->search}%"))
            ->orderBy('full_name');

        return [
            'doctors' => $query->paginate(10),
        ];
    }

    public function toggleActive(int $doctor): void
    {
        $doc = Doctor::findOrFail($doctor);
        $doc->is_active = ! $doc->is_active;
        $doc->save();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Doctors</h1>
        <flux:link :href="route('admin.doctors.create')" wire:navigate>
            <flux:button variant="primary">Add Doctor</flux:button>
        </flux:link>
    </div>

    <flux:input wire:model.debounce.300ms="search" placeholder="Search by name, specialty, license..." />

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left">
                    <th class="p-2">Name</th>
                    <th class="p-2">Specialty</th>
                    <th class="p-2">License</th>
                    <th class="p-2">Contact</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Active</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $d)
                <tr class="border-t">
                    <td class="p-2">{{ $d->full_name }}</td>
                    <td class="p-2">{{ $d->specialty }}</td>
                    <td class="p-2">{{ $d->license_number }}</td>
                    <td class="p-2">{{ $d->contact_number }}</td>
                    <td class="p-2">{{ $d->email }}</td>
                    <td class="p-2">
                        <flux:switch wire:click="toggleActive({{ $d->id }})" :checked="$d->is_active" />
                    </td>
                    <td class="p-2 space-x-2">
                        <flux:link :href="route('admin.doctors.edit', $d)" wire:navigate>
                            <flux:button size="sm">Edit</flux:button>
                        </flux:link>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $doctors->links() }}
    </div>
</div>
