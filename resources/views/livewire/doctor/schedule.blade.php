<?php

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Notifications\ScheduleChanged;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $view = 'week'; // 'week' | 'month'
    public string $start = '';
    public string $end = '';

    // Form fields
    public string $hospital_name = '';
    public ?string $date = null; // one-time
    public ?int $weekday = null; // recurring 0-6
    public string $start_time = '';
    public string $end_time = '';
    public string $breaks = '';// e.g. 12:00-13:00;15:30-15:45
    public bool $is_exception = false;

    public function mount(): void
    {
        $this->start = now()->startOfWeek()->toDateString();
        $this->end = now()->endOfWeek()->toDateString();
    }

    protected function doctor(): ?Doctor
    {
        return Doctor::where('user_id', auth()->id())->first();
    }

    public function with(): array
    {
        $doctor = $this->doctor();
        $schedules = collect();
        if ($doctor) {
            $query = DoctorSchedule::where('doctor_id', $doctor->id);
            if ($this->view === 'week') {
                $query->where(function($q){
                    $q->whereBetween('date', [$this->start, $this->end])
                      ->orWhereNotNull('weekday');
                });
            } else {
                $monthStart = Carbon::parse($this->start)->startOfMonth()->toDateString();
                $monthEnd = Carbon::parse($this->start)->endOfMonth()->toDateString();
                $query->where(function($q) use ($monthStart, $monthEnd){
                    $q->whereBetween('date', [$monthStart, $monthEnd])
                      ->orWhereNotNull('weekday');
                });
            }
            $schedules = $query->orderBy('date')->orderBy('weekday')->orderBy('start_time')->get();
        }

        return compact('schedules');
    }

    public function setRange(string $start): void
    {
        $this->start = $start;
        $this->end = Carbon::parse($start)->addDays($this->view === 'week' ? 6 : 30)->toDateString();
    }

    public function addSlot(): void
    {
        $doctor = $this->doctor();
        if (!$doctor) return;

        $validated = $this->validate([
            'hospital_name' => ['required','string','max:255'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'date' => ['nullable','date'],
            'weekday' => ['nullable','integer','between:0,6'],
            'breaks' => ['nullable','string'],
            'is_exception' => ['boolean'],
        ]);

        $breaks = [];
        if (!empty($this->breaks)) {
            $parts = array_filter(array_map('trim', explode(';', $this->breaks)));
            foreach ($parts as $p) {
                if (str_contains($p, '-')) {
                    [$bs, $be] = array_map('trim', explode('-', $p));
                    $breaks[] = ['start' => $bs, 'end' => $be];
                }
            }
        }

        $schedule = DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'hospital_name' => $this->hospital_name,
            'date' => $this->date,
            'weekday' => $this->weekday,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'breaks' => $breaks,
            'recurrence_rule' => $this->weekday !== null ? 'WEEKLY' : null,
            'is_exception' => $this->is_exception,
            'is_available' => true,
        ]);

        if ($doctor->user) {
            Notification::route('mail', $doctor->email ?? $doctor->user->email)
                ->notify(new ScheduleChanged('created', $schedule));
        }

        $this->reset(['hospital_name','date','weekday','start_time','end_time','breaks','is_exception']);
    }

    public function toggleAvailability(int $id): void
    {
        $schedule = DoctorSchedule::findOrFail($id);
        $schedule->is_available = !$schedule->is_available;
        $schedule->save();

        $doctor = $this->doctor();
        if ($doctor && $doctor->user) {
            Notification::route('mail', $doctor->email ?? $doctor->user->email)
                ->notify(new ScheduleChanged('availability toggled', $schedule));
        }
    }

    public function deleteSlot(int $id): void
    {
        $schedule = DoctorSchedule::findOrFail($id);
        $schedule->delete();

        $doctor = $this->doctor();
        if ($doctor && $doctor->user) {
            Notification::route('mail', $doctor->email ?? $doctor->user->email)
                ->notify(new ScheduleChanged('deleted', $schedule));
        }
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Schedule</h1>
        <div class="flex items-center gap-2">
            <flux:button wire:click="$set('view','week')" :variant="$view==='week' ? 'primary' : 'outline'">Week</flux:button>
            <flux:button wire:click="$set('view','month')" :variant="$view==='month' ? 'primary' : 'outline'">Month</flux:button>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-500">Start:</span>
                    <input type="date" class="border rounded px-2 py-1" wire:model.live="start" />
                </div>
                <div class="text-sm text-zinc-500">Showing: {{ strtoupper($view) }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th class="p-2 text-left">Date/Weekday</th>
                            <th class="p-2 text-left">Hospital</th>
                            <th class="p-2 text-left">Time</th>
                            <th class="p-2 text-left">Breaks</th>
                            <th class="p-2 text-left">Avail</th>
                            <th class="p-2 text-left">Type</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $s)
                        <tr class="border-t">
                            <td class="p-2">{{ $s->date ? $s->date : (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$s->weekday] ?? '-') }}</td>
                            <td class="p-2">{{ $s->hospital_name }}</td>
                            <td class="p-2">{{ $s->start_time }} - {{ $s->end_time }}</td>
                            <td class="p-2">
                                @if($s->breaks)
                                    @foreach($s->breaks as $b)
                                        <span class="inline-block text-xs bg-zinc-100 dark:bg-zinc-800 rounded px-2 py-0.5 mr-1">{{ $b['start'] }}-{{ $b['end'] }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="p-2">
                                <flux:switch wire:click="toggleAvailability({{ $s->id }})" :checked="$s->is_available" />
                            </td>
                            <td class="p-2">{{ $s->is_exception ? 'Exception' : ($s->weekday !== null ? 'Recurring' : 'One-time') }}</td>
                            <td class="p-2">
                                <flux:button size="sm" variant="destructive" wire:click="deleteSlot({{ $s->id }})">Delete</flux:button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg p-4 shadow">
            <h2 class="text-lg font-medium mb-3">Add Slot</h2>
            <div class="space-y-3">
                <flux:input wire:model="hospital_name" label="Hospital" placeholder="The National Hospital, Sri Lanka" />
                <div class="grid grid-cols-2 gap-3">
                    <flux:input wire:model="date" type="date" label="Date (one-time)" />
                    <div>
                        <label class="text-sm font-medium">Weekday (recurring)</label>
                        <select wire:model="weekday" class="w-full border rounded px-2 py-1">
                            <option value="">—</option>
                            <option value="0">Sun</option>
                            <option value="1">Mon</option>
                            <option value="2">Tue</option>
                            <option value="3">Wed</option>
                            <option value="4">Thu</option>
                            <option value="5">Fri</option>
                            <option value="6">Sat</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <flux:input wire:model="start_time" type="time" label="Start time" />
                    <flux:input wire:model="end_time" type="time" label="End time" />
                </div>
                <flux:textarea wire:model="breaks" label="Breaks (e.g. 12:00-13:00;15:30-15:45)" />
                <div class="flex items-center gap-2">
                    <flux:switch wire:model="is_exception" />
                    <span>Mark as Exception</span>
                </div>
                <flux:button variant="primary" wire:click="addSlot">Save Slot</flux:button>
            </div>
        </div>
    </div>
</div>
