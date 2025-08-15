<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
	public string $date = '';
	public string $department = '';
	public string $search = '';

	public function mount(): void
	{
		$this->date = now()->toDateString();
	}

	public function with(): array
	{
		$doctors = Doctor::with(['user'])->where('is_active', true)
			->when($this->search, function($q) {
				$term = "%{$this->search}%";
				$q->where('full_name', 'like', $term)
				  ->orWhere('specialty', 'like', $term)
				  ->orWhere('license_number', 'like', $term);
			})
			->orderBy('full_name')
			->get();

		// For each doctor, find a schedule on the selected date
		$cards = $doctors->map(function($d) {
			$schedule = DoctorSchedule::where('doctor_id', $d->id)
				->where(function($q) {
					$q->whereDate('date', $this->date)
					  ->orWhere('weekday', \Carbon\Carbon::parse($this->date)->dayOfWeek);
				})
				->orderBy('start_time')
				->first();

			return [
				'doctor' => $d,
				'hospital' => $schedule?->hospital_name,
				'time' => $schedule ? sprintf('%s - %s', $schedule->start_time, $schedule->end_time) : null,
				'available' => (bool)($schedule?->is_available),
			];
		});

		return compact('cards');
	}
};
?>

<div class="space-y-6">
	<h1 class="text-2xl font-semibold">Admin Dashboard</h1>

	<div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
		<div class="flex flex-wrap items-center gap-3">
			<flux:input type="date" wire:model="date" label="Date"/>
			<flux:input class="flex-1" wire:model.debounce.300ms="search" placeholder="Search by name, registration, NIC..." />
		</div>
	</div>

	<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
		@foreach($cards as $c)
			<div class="rounded-xl border bg-white dark:bg-zinc-900 p-4 shadow">
				<div class="flex items-center gap-3">
					<div class="w-14 h-14 rounded-full bg-zinc-200 overflow-hidden">
						@if($c['doctor']->profile_photo_path)
							<img class="w-14 h-14 object-cover" src="{{ Storage::disk('public')->url($c['doctor']->profile_photo_path) }}" alt="Doctor"/>
						@endif
					</div>
					<div>
						<div class="font-semibold">{{ $c['doctor']->full_name }}</div>
						<div class="text-sm text-zinc-500">{{ $c['doctor']->specialty }}</div>
					</div>
				</div>
				<div class="mt-3 text-sm text-zinc-600">
					<div>{{ $c['hospital'] ?? 'Hospital — N/A' }}</div>
					<div>{{ $c['time'] ?? 'Time — N/A' }}</div>
				</div>
				<div class="mt-4 flex items-center justify-between">
					<div class="text-xs {{ $c['available'] ? 'text-green-600' : 'text-zinc-500' }}">
						{{ $c['available'] ? 'Open' : 'Closed' }}
					</div>
					<flux:link :href="route('admin.appointments.index')" wire:navigate>
						<flux:button variant="destructive">View Appointments</flux:button>
					</flux:link>
				</div>
			</div>
		@endforeach
	</div>
</div>
