<x-app-layout>
    <x-slot name="title">
        {{ __('Complaint Details') }}
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ __('Complaint #') }}{{ $complaint->id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-zinc-400">
                            {{ __('Submitted on') }} {{ $complaint->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <a href="{{ route('complaints.index') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Complaint Details -->
                    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('Complaint Details') }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                    {{ __('Category') }}
                                </label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst($complaint->category) }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                    {{ __('Description') }}
                                </label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                                    {{ $complaint->description }}
                                </p>
                            </div>

                            @if($complaint->photo_path)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                        {{ __('Photo') }}
                                    </label>
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($complaint->photo_path) }}" 
                                             alt="Complaint photo" 
                                             class="max-w-md rounded-lg shadow-sm">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Updates (for staff/admin/doctor) -->
                    @if(auth()->user()->type !== 'patient' && auth()->user()->can('update', $complaint))
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('Update Status') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('complaints.status', $complaint) }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                                {{ __('Status') }}
                                            </label>
                                            <select id="status" name="status" required 
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                <option value="new" {{ $complaint->status === 'new' ? 'selected' : '' }}>New</option>
                                                <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="closed" {{ $complaint->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                {{ __('Update Status') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Feedback Form (for patients when resolved) -->
                    @if(auth()->user()->type === 'patient' && $complaint->isResolved() && !$complaint->rating)
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('Rate Your Experience') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('complaints.feedback', $complaint) }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                                {{ __('Rating') }}
                                            </label>
                                            <div class="mt-2 flex items-center space-x-4">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <label class="flex items-center">
                                                        <input type="radio" name="rating" value="{{ $i }}" required
                                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                        <span class="ml-2 text-sm text-gray-700 dark:text-zinc-300">{{ $i }}</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div>
                                            <label for="feedback" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                                {{ __('Additional Feedback (Optional)') }}
                                            </label>
                                            <textarea id="feedback" name="feedback" rows="3"
                                                      class="mt-1 block w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                      placeholder="{{ __('Share your experience with the resolution...') }}">{{ old('feedback') }}</textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                {{ __('Submit Feedback') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Feedback Display -->
                    @if($complaint->rating)
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('Patient Feedback') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $complaint->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-zinc-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-zinc-400">
                                        {{ $complaint->rating }}/5
                                    </span>
                                </div>
                                @if($complaint->feedback)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            "{{ $complaint->feedback }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('Status') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            @php
                                $statusColors = [
                                    'new' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                ];
                                $statusLabels = [
                                    'new' => 'New',
                                    'in_progress' => 'In Progress',
                                    'resolved' => 'Resolved',
                                    'closed' => 'Closed'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$complaint->status] }}">
                                {{ $statusLabels[$complaint->status] }}
                            </span>
                            
                            @if($complaint->resolved_at)
                                <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                                    {{ __('Resolved on') }} {{ $complaint->resolved_at->format('M d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Patient Information -->
                    @if(auth()->user()->type !== 'patient')
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('Patient Information') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                            {{ __('Name') }}
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $complaint->user->name }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                            {{ __('Email') }}
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $complaint->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Assignment Information -->
                    @if($complaint->assigned_to)
                        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ __('Assigned To') }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                            {{ __('Name') }}
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $complaint->assignedUser->name }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300">
                                            {{ __('Department') }}
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst($complaint->assignedUser->type) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
