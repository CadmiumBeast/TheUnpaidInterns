<?php

use App\Models\Appointment;
use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $doctorId = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function with(): array
    {
        $doctors = Doctor::orderBy('full_name')->get(['id','full_name']);
        $appointments = Appointment::with('doctor')
            ->when($this->doctorId, fn($q) => $q->where('doctor_id', $this->doctorId))
            ->when($this->dateFrom, fn($q) => $q->whereDate('scheduled_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('scheduled_date', '<=', $this->dateTo))
            ->orderBy('scheduled_date')->orderBy('start_time')
            ->paginate(15);
        return compact('appointments','doctors');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-xl font-semibold">Appointments</h1>

    <div class="grid md:grid-cols-4 gap-4">
        <flux:select wire:model="doctorId" label="Doctor">
            <option value="">All</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}">{{ $d->full_name }}</option>
            @endforeach
        </flux:select>
        <flux:input type="date" wire:model="dateFrom" label="From" />
        <flux:input type="date" wire:model="dateTo" label="To" />
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left">
                    <th class="p-2">Date</th>
                    <th class="p-2">Time</th>
                    <th class="p-2">Doctor</th>
                    <th class="p-2">Patient</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $a)
                <tr class="border-t">
                    <td class="p-2">{{ $a->scheduled_date }}</td>
                    <td class="p-2">{{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}</td>
                    <td class="p-2">{{ $a->doctor?->full_name }}</td>
                    <td class="p-2">#{{ $a->patient_id }}</td>
                    <td class="p-2">{{ $a->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $appointments->links() }}
    </div>
</div>
