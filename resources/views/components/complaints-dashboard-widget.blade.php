@php
    $user = auth()->user();
    $complaints = collect();
    
    if ($user->type === 'patient') {
        $complaints = $user->complaints;
    } elseif (in_array($user->type, ['admin', 'staff', 'doctor'])) {
        $complaints = \App\Models\Complaint::byDepartment($user->type);
    }
    
    $totalComplaints = $complaints->count();
    $newComplaints = $complaints->where('status', 'new')->count();
    $inProgressComplaints = $complaints->where('status', 'in_progress')->count();
    $resolvedComplaints = $complaints->where('status', 'resolved')->count();
@endphp

<div class="bg-white dark:bg-zinc-800 overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400 truncate">
                        {{ __('Complaints') }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $totalComplaints }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 dark:bg-zinc-700 px-5 py-3">
        <div class="text-sm">
            <div class="flex justify-between text-gray-500 dark:text-zinc-400">
                <span>{{ __('New') }}: {{ $newComplaints }}</span>
                <span>{{ __('In Progress') }}: {{ $inProgressComplaints }}</span>
                <span>{{ __('Resolved') }}: {{ $resolvedComplaints }}</span>
            </div>
        </div>
    </div>
</div>
