<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $doctorId = '';
    public $hospital = '';

    public function with(): array
    {
        $doctors = Doctor::orderBy('full_name')->get(['id','full_name']);
        $schedules = DoctorSchedule::with('doctor')
            ->when($this->doctorId, fn($q) => $q->where('doctor_id', $this->doctorId))
            ->when($this->hospital, fn($q) => $q->where('hospital_name', 'like', "%{$this->hospital}%"))
            ->orderBy('doctor_id')->orderBy('date')->orderBy('weekday')->get();
        return compact('doctors','schedules');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Schedules</h1>
        <flux:link :href="route('doctor.schedules')" wire:navigate>
            <flux:button variant="primary">Open Doctor Calendar</flux:button>
        </flux:link>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <flux:select wire:model="doctorId" label="Filter by Doctor">
            <option value="">All</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}">{{ $d->full_name }}</option>
            @endforeach
        </flux:select>
        <flux:input wire:model="hospital" label="Hospital contains" />
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left">
                    <th class="p-2">Doctor</th>
                    <th class="p-2">Hospital</th>
                    <th class="p-2">Date/Weekday</th>
                    <th class="p-2">Time</th>
                    <th class="p-2">Available</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $s)
                <tr class="border-t">
                    <td class="p-2">{{ $s->doctor->full_name }}</td>
                    <td class="p-2">{{ $s->hospital_name }}</td>
                    <td class="p-2">{{ $s->date ? $s->date->format('Y-m-d') : 'Weekday '.$s->weekday }}</td>
                    <td class="p-2">{{ $s->start_time }} - {{ $s->end_time }}</td>
                    <td class="p-2">{{ $s->is_available ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
