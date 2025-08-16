<?php

use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Appointment $appointment;
    public string $notes = '';
    public array $prescriptionItems = [];

    public function mount(Appointment $appointment): void
    {
        $this->appointment = $appointment;
        $this->authorizeAccess();
        $this->notes = (string) ($appointment->notes ?? '');
        $this->prescriptionItems = [];
    }

    protected function authorizeAccess(): void
    {
        $doctor = \App\Models\Doctor::where('user_id', auth()->id())->first();
        abort_unless($doctor && $doctor->id === $this->appointment->doctor_id, 403);
    }

    public function save(): void
    {
        $this->authorizeAccess();
        $this->validate([
            'notes' => ['nullable','string','max:2000'],
        ]);
        $this->appointment->notes = $this->notes;
        $this->appointment->save();
        session()->flash('status', 'Notes saved');
    }

    public function addPrescriptionItem(): void
    {
        $this->prescriptionItems[] = ['medicine' => '', 'dose' => '', 'frequency' => '', 'duration' => ''];
    }

    public function removePrescriptionItem(int $idx): void
    {
        unset($this->prescriptionItems[$idx]);
        $this->prescriptionItems = array_values($this->prescriptionItems);
    }

    public function savePrescription(): void
    {
        $this->authorizeAccess();
        $this->validate([
            'prescriptionItems' => ['array','max:50'],
            'prescriptionItems.*.medicine' => ['required','string','max:255'],
            'prescriptionItems.*.dose' => ['nullable','string','max:255'],
            'prescriptionItems.*.frequency' => ['nullable','string','max:255'],
            'prescriptionItems.*.duration' => ['nullable','string','max:255'],
        ]);
        \App\Models\Prescription::create([
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->appointment->doctor_id,
            'patient_id' => $this->appointment->patient_id,
            'items' => $this->prescriptionItems,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);
        $this->prescriptionItems = [];
        session()->flash('status', 'Prescription saved');
    }

    public function with(): array
    {
        $this->appointment->load(['doctor','patient','schedule','prescriptions']);
        $pastAppointments = Appointment::with('prescriptions')
            ->where('patient_id', $this->appointment->patient_id)
            ->where('doctor_id', $this->appointment->doctor_id)
            ->whereDate('scheduled_date', '<', $this->appointment->scheduled_date)
            ->orderByDesc('scheduled_date')
            ->limit(10)
            ->get();
        return compact('pastAppointments');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Appointment Detail</h1>
    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><span class="text-zinc-500">Patient:</span> <span class="font-medium">{{ $appointment->patient?->name ?? ('#'.$appointment->patient_id) }}</span></div>
            <div><span class="text-zinc-500">Doctor:</span> <span class="font-medium">{{ $appointment->doctor?->full_name }}</span></div>
            <div><span class="text-zinc-500">Date:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('Y-m-d') }}</span></div>
            <div><span class="text-zinc-500">Time:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</span></div>
            <div class="md:col-span-2"><span class="text-zinc-500">Hospital:</span> <span class="font-medium">{{ $appointment->schedule?->hospital_name ?? '—' }}</span></div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-2">Prescription / Notes</h2>
        <form wire:submit.prevent="save" class="space-y-3 mb-4">
            <textarea wire:model.defer="notes" rows="4" class="w-full border rounded p-2" placeholder="General notes"></textarea>
            <flux:button type="submit" variant="secondary">Save Notes</flux:button>
        </form>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="font-medium">Prescription Items</h3>
                <flux:button size="sm" variant="primary" wire:click="addPrescriptionItem">Add Item</flux:button>
            </div>
            @forelse($prescriptionItems as $i => $item)
                <div class="grid md:grid-cols-4 gap-2">
                    <input type="text" class="border rounded p-2" placeholder="Medicine" wire:model.defer="prescriptionItems.{{ $i }}.medicine" />
                    <input type="text" class="border rounded p-2" placeholder="Dose" wire:model.defer="prescriptionItems.{{ $i }}.dose" />
                    <input type="text" class="border rounded p-2" placeholder="Frequency" wire:model.defer="prescriptionItems.{{ $i }}.frequency" />
                    <div class="flex gap-2">
                        <input type="text" class="border rounded p-2 flex-1" placeholder="Duration" wire:model.defer="prescriptionItems.{{ $i }}.duration" />
                        <flux:button size="sm" variant="danger" wire:click="removePrescriptionItem({{ $i }})">Remove</flux:button>
                    </div>
                </div>
            @empty
                <div class="text-sm text-zinc-500">No items yet.</div>
            @endforelse
            <div class="mt-2">
                <flux:button variant="primary" wire:click="savePrescription">Save Prescription</flux:button>
            </div>
        </div>
        @if (session('status'))
            <div class="text-green-600 text-sm mt-2">{{ session('status') }}</div>
        @endif
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-2">Past Appointments & Prescriptions</h2>
        <div class="divide-y">
            @forelse($pastAppointments as $pa)
                <div class="py-2">
                    <div class="text-sm">{{ \Carbon\Carbon::parse($pa->scheduled_date)->format('Y-m-d') }} • {{ \Carbon\Carbon::parse($pa->start_time)->format('H:i') }} — Prescriptions: {{ $pa->prescriptions->count() }}</div>
                </div>
            @empty
                <div class="text-sm text-zinc-500">No past appointments found.</div>
            @endforelse
        </div>
    </div>
</div>
