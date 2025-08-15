<?php

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $today;

    public function mount(): void
    {
        $this->today = now()->toDateString();
    }

    public function with(): array
    {
        $doctorId = auth()->user()?->id;
        // In a real setup, map user -> doctor via doctors.user_id
        $doctor = \App\Models\Doctor::where('user_id', $doctorId)->first();
        $id = $doctor?->id ?? 0;

        $todayAppointments = Appointment::with('doctor')
            ->whereDate('scheduled_date', $this->today)
            ->where('doctor_id', $id)
            ->orderBy('start_time')
            ->get();

        $upcoming = Appointment::with('doctor')
            ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->where('doctor_id', $id)
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        return compact('todayAppointments', 'upcoming');
    }
};
?>

<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Doctor Dashboard</h1>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Today's Patients</h2>
            <ul class="space-y-2">
                @forelse($todayAppointments as $a)
                    <li class="flex items-center justify-between border-b py-2">
                        <div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}</div>
                            <div class="text-sm text-zinc-500">Patient #{{ $a->patient_id }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:link href="#" class="text-teal-600">View Record</flux:link>
                        </div>
                    </li>
                @empty
                    <li class="text-zinc-500">No appointments today.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Upcoming (7 days)</h2>
            <ul class="space-y-2">
                @forelse($upcoming as $a)
                    <li class="flex items-center justify-between border-b py-2">
                        <div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($a->scheduled_date)->format('Y-m-d') }} {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}</div>
                            <div class="text-sm text-zinc-500">Patient #{{ $a->patient_id }}</div>
                        </div>
                    </li>
                @empty
                    <li class="text-zinc-500">No upcoming appointments.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
        <h2 class="text-lg font-medium mb-3">Schedule Overview</h2>
        <p class="text-zinc-500">Scheduling UI to be added: weekly/monthly calendar, manage availability, breaks, exceptions.</p>
    </div>
</div>
