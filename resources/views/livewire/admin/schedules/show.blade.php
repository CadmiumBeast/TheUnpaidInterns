<?php

use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public DoctorSchedule $schedule;

    public function mount(DoctorSchedule $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function with(): array
    {
        $date = request()->query('date');
        $appointments = collect();
        if ($date) {
            $appointments = Appointment::with('patient')
                ->where('schedule_id', $this->schedule->id)
                ->whereDate('scheduled_date', $date)
                ->orderBy('start_time')
                ->get();
        }
        return compact('appointments','date');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Schedule Details</h1>
    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <div class="text-sm">Doctor ID: {{ $schedule->doctor_id }} • Hospital: {{ $schedule->hospital_name ?? '—' }}</div>
        <div class="text-sm">Start: {{ $schedule->start_time }} • End: {{ $schedule->end_time }}</div>
        <div class="text-sm">Capacity: {{ $schedule->capacity ?? 25 }}</div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-2">Booked Patients ({{ $date ?? 'Select date' }})</h2>
        @if(!$date)
            <div class="text-sm text-zinc-500">Provide a date=YYYY-MM-DD query parameter to view bookings for a specific date.</div>
        @else
            <div class="divide-y">
                @forelse($appointments as $ap)
                    <div class="py-2 flex items-center justify-between">
                        <div class="text-sm">{{ \Carbon\Carbon::parse($ap->start_time)->format('H:i') }} — {{ $ap->patient?->name ?? ('#'.$ap->patient_id) }}</div>
                        <div class="text-xs text-zinc-500">Status: {{ $ap->status }}</div>
                    </div>
                @empty
                    <div class="text-sm text-zinc-500">No patients booked for this date.</div>
                @endforelse
            </div>
        @endif
    </div>
</div>
