<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
	public string $date = '';
	public string $department = '';
	public string $search = '';
	public string $doctorId = '';
	public string $timespan = 'day'; // day | week

	public function mount(): void
	{
		$this->date = now()->toDateString();
	}

	public function with(): array
	{
		$date = $this->date ?: now()->toDateString();
		$start = \Carbon\Carbon::parse($date);
		$days = $this->timespan === 'week' ? 7 : 1;
		$end = $start->copy()->addDays($days - 1);

		$doctorsBase = Doctor::query()
			->where('is_active', true)
			->when($this->department, fn($q) => $q->where('specialty', $this->department))
			->when($this->search, function($q) {
				$term = "%{$this->search}%";
				$q->where(function($qq) use ($term) {
					$qq->where('full_name', 'like', $term)
					   ->orWhere('license_number', 'like', $term);
				});
			});
		$doctors = (clone $doctorsBase)
			->when($this->doctorId, fn($q) => $q->where('id', $this->doctorId))
			->orderBy('full_name')
			->get(['id','full_name','specialty','profile_photo_path']);

		$doctorOptions = (clone $doctorsBase)
			->orderBy('full_name')
			->get(['id','full_name']);

		$cards = collect();
		for ($i = 0; $i < $days; $i++) {
			$currentDate = $start->copy()->addDays($i)->toDateString();
			$w = \Carbon\Carbon::parse($currentDate)->dayOfWeek;
			foreach ($doctors as $doctor) {
				$schedules = DoctorSchedule::where('doctor_id', $doctor->id)
					->where(function($q) use ($currentDate, $w) {
						$q->whereDate('date', $currentDate)
						  ->orWhere('weekday', $w);
					})
					->where('is_available', true)
					->where('is_exception', false)
					->orderBy('start_time')
					->get();

				foreach ($schedules as $schedule) {
					$max = $schedule->capacity ?? 25;
					$booked = Appointment::where('doctor_id', $doctor->id)
						->whereDate('scheduled_date', $currentDate)
						->where('start_time', $schedule->start_time)
						->count();
					$cards->push([
						'doctor' => $doctor,
						'schedule' => $schedule,
						'booked' => $booked,
						'max' => $max,
						'date' => $currentDate,
					]);
				}
			}
		}

		// Pending appointments in the selected range (status booked/pending)
		$pendingAppointments = Appointment::with([
			'doctor:id,full_name,specialty,profile_photo_path',
			'patient:id,name,email',
			'schedule:id,doctor_id,hospital_name,start_time'
		])
			->whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])
			->whereIn('status', ['booked', 'pending'])
			->when($this->doctorId, fn($q) => $q->where('doctor_id', $this->doctorId))
			->when($this->department, fn($q) => $q->whereHas('doctor', fn($qq) => $qq->where('specialty', $this->department)))
			->when($this->search, function($q) {
				$term = "%{$this->search}%";
				$q->where(function($qq) use ($term) {
					$qq->whereHas('doctor', function($qd) use ($term) {
						$qd->where('full_name', 'like', $term)
						   ->orWhere('license_number', 'like', $term);
					})
					->orWhereHas('patient', function($qp) use ($term) {
						$qp->where('name', 'like', $term)
						   ->orWhere('email', 'like', $term);
					});
				});
			})
			->orderBy('scheduled_date')
			->orderBy('start_time')
			->limit(50)
			->get(['id','patient_id','doctor_id','schedule_id','scheduled_date','start_time','status','notes']);

		// Doctor absences/closures within the selected range
		$absences = collect();
		for ($i = 0; $i < $days; $i++) {
			$currentDate = $start->copy()->addDays($i)->toDateString();
			$w = \Carbon\Carbon::parse($currentDate)->dayOfWeek;
			foreach ($doctors as $doctor) {
				$leaveSchedules = DoctorSchedule::where('doctor_id', $doctor->id)
					->where(function($q) use ($currentDate, $w) {
						$q->whereDate('date', $currentDate)
						  ->orWhere('weekday', $w);
					})
					->where(function($qq){
						$qq->where('is_exception', true)
						   ->orWhere('is_available', false);
					})
					->orderBy('start_time')
					->get();

				foreach ($leaveSchedules as $ls) {
					$absences->push([
						'doctor' => $doctor,
						'schedule' => $ls,
						'date' => $currentDate,
					]);
				}
			}
		}

		$metrics = [
			'pending' => $pendingAppointments->count(),
			'absences' => $absences->count(),
			'activeDoctors' => $doctors->count(),
		];

		return [
			'cards' => $cards,
			'date' => $date,
			'doctorOptions' => $doctorOptions,
			'pendingAppointments' => $pendingAppointments,
			'absences' => $absences,
			'metrics' => $metrics,
		];
	}
};
?>

<div class="space-y-6">
	<h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>

	<!-- Summary Metrics -->
	<div class="grid sm:grid-cols-3 gap-4">
		<div class="rounded-lg border bg-white dark:bg-zinc-900 p-4">
			<div class="text-sm text-zinc-500">Pending Appointments</div>
			<div class="text-2xl font-semibold mt-1">{{ $metrics['pending'] }}</div>
		</div>
		<div class="rounded-lg border bg-white dark:bg-zinc-900 p-4">
			<div class="text-sm text-zinc-500">Doctor Absences</div>
			<div class="text-2xl font-semibold mt-1">{{ $metrics['absences'] }}</div>
		</div>
		<div class="rounded-lg border bg-white dark:bg-zinc-900 p-4">
			<div class="text-sm text-zinc-500">Active Doctors</div>
			<div class="text-2xl font-semibold mt-1">{{ $metrics['activeDoctors'] }}</div>
		</div>
	</div>

	<div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
		<div class="flex flex-wrap items-end gap-4">
			<div>
				<label class="text-sm block mb-1">Date</label>
				<input type="date" class="border rounded p-2" wire:model="date" />
			</div>
			<div>
				<label class="text-sm block mb-1">View</label>
				<select class="border rounded p-2" wire:model="timespan">
					<option value="day">Day</option>
					<option value="week">Week</option>
				</select>
			</div>
			<div>
				<label class="text-sm block mb-1">All Departments</label>
				<select class="border rounded p-2" wire:model="department">
					<option value="">All Departments</option>
					@foreach(config('specialties.list', []) as $spec)
						<option value="{{ $spec }}">{{ $spec }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="text-sm block mb-1">Doctor</label>
				<select class="border rounded p-2" wire:model="doctorId">
					<option value="">All</option>
					@foreach($doctorOptions as $d)
						<option value="{{ $d->id }}">{{ $d->full_name }}</option>
					@endforeach
				</select>
			</div>
			<div class="flex-1 min-w-[240px]">
				<label class="text-sm block mb-1">Search</label>
				<input type="text" class="border rounded p-2 w-full" placeholder="Search by name, registration, NIC..." wire:model.debounce.300ms="search" />
			</div>
		</div>
	</div>

	<!-- Available Slots Cards -->
	<div class="grid md:grid-cols-3 gap-6 mt-6">
		@if($cards->isEmpty())
			<div class="md:col-span-3 text-center text-zinc-500 py-12">No slots for the selected filters.</div>
		@endif
		@foreach($cards as $c)
		<div class="rounded-2xl border bg-white dark:bg-zinc-900 p-5 shadow">
			<div class="flex items-center gap-4">
				<div class="w-16 h-16 rounded-full bg-zinc-200 overflow-hidden">
					@if($c['doctor']->profile_photo_path)
						<img class="w-16 h-16 object-cover" src="{{ Storage::disk('public')->url($c['doctor']->profile_photo_path) }}" alt="Doctor"/>
					@endif
				</div>
				<div class="flex-1">
					<div class="font-semibold text-lg">{{ $c['doctor']->full_name }}</div>
					<div class="text-sm text-zinc-500">{{ $c['doctor']->specialty }}</div>
					<div class="mt-1 text-xs text-zinc-500">{{ $c['schedule']->hospital_name ?? 'Hospital — N/A' }}</div>
				</div>
				<div class="text-xs px-3 py-1 rounded-full {{ $c['booked'] < $c['max'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
					{{ $c['booked'] < $c['max'] ? 'Open' : 'Full' }}
				</div>
			</div>
			<div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
				<div>
					<div class="text-zinc-500">Date</div>
					<div class="font-medium">{{ \Carbon\Carbon::parse($c['date'])->format('d/m/Y') }}</div>
				</div>
				<div>
					<div class="text-zinc-500">Time</div>
					<div class="font-medium">{{ \Carbon\Carbon::parse($c['schedule']->start_time)->format('h:i A') }}</div>
				</div>
				<div>
					<div class="text-zinc-500">Booked</div>
					<div class="font-medium">{{ $c['booked'] }}/{{ $c['max'] }}</div>
				</div>
			</div>
			<div class="mt-4">
				<form method="POST" action="{{ route('admin.appointments.reserve') }}" class="w-full">
					@csrf
					<input type="hidden" name="doctor_id" value="{{ $c['doctor']->id }}" />
					<input type="hidden" name="schedule_id" value="{{ $c['schedule']->id }}" />
					<input type="hidden" name="date" value="{{ $c['date'] }}" />
					<input type="hidden" name="start_time" value="{{ $c['schedule']->start_time }}" />
					<button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-red-500 text-white py-2.5 px-4 disabled:opacity-50" @disabled($c['booked'] >= $c['max'])>
						Reserve Appointment
					</button>
				</form>
			</div>
		</div>
		@endforeach
	</div>

	<!-- Pending Appointments List -->
	<div class="mt-8">
		<h2 class="text-xl font-semibold mb-3">Pending Appointments</h2>
		<div class="rounded-lg border bg-white dark:bg-zinc-900">
			@if($pendingAppointments->isEmpty())
				<div class="p-6 text-center text-zinc-500">No pending appointments in this range.</div>
			@else
				<div class="divide-y">
					@foreach($pendingAppointments as $ap)
						<div class="p-4 flex items-center gap-4">
							<div class="w-10 h-10 rounded-full bg-zinc-200 overflow-hidden">
								@if($ap->doctor && $ap->doctor->profile_photo_path)
									<img class="w-10 h-10 object-cover" src="{{ Storage::disk('public')->url($ap->doctor->profile_photo_path) }}" alt="Doctor"/>
								@endif
							</div>
							<div class="flex-1">
								<div class="font-medium">{{ $ap->doctor?->full_name }} <span class="text-xs text-zinc-500">({{ $ap->doctor?->specialty }})</span></div>
								<div class="text-sm text-zinc-600">Patient: {{ $ap->patient?->name ?? 'Unassigned' }}</div>
							</div>
							<div class="text-right text-sm">
								<div>{{ \Carbon\Carbon::parse($ap->scheduled_date)->format('d/m/Y') }} • {{ \Carbon\Carbon::parse($ap->start_time)->format('h:i A') }}</div>
								<div class="text-xs text-zinc-500">{{ $ap->schedule?->hospital_name ?? 'Hospital — N/A' }}</div>
							</div>
						</div>
					@endforeach
				</div>
			@endif
		</div>
	</div>

	<!-- Doctor Absences -->
	<div class="mt-8">
		<h2 class="text-xl font-semibold mb-3">Doctor Absences</h2>
		<div class="rounded-lg border bg-white dark:bg-zinc-900">
			@if($absences->isEmpty())
				<div class="p-6 text-center text-zinc-500">No absences/closures in this range.</div>
			@else
				<div class="divide-y">
					@foreach($absences as $a)
						<div class="p-4 flex items-center gap-4">
							<div class="w-10 h-10 rounded-full bg-zinc-200 overflow-hidden">
								@if($a['doctor']->profile_photo_path)
									<img class="w-10 h-10 object-cover" src="{{ Storage::disk('public')->url($a['doctor']->profile_photo_path) }}" alt="Doctor"/>
								@endif
							</div>
							<div class="flex-1">
								<div class="font-medium">{{ $a['doctor']->full_name }} <span class="text-xs text-zinc-500">({{ $a['doctor']->specialty }})</span></div>
								<div class="text-sm text-zinc-600">{{ $a['schedule']->hospital_name ?? 'Hospital — N/A' }}</div>
							</div>
							<div class="text-right text-sm">
								<div>{{ \Carbon\Carbon::parse($a['date'])->format('d/m/Y') }} • {{ \Carbon\Carbon::parse($a['schedule']->start_time)->format('h:i A') }}</div>
								<div class="text-xs inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-200 text-zinc-700">Closed</div>
							</div>
						</div>
					@endforeach
				</div>
			@endif
		</div>
	</div>
</div>
