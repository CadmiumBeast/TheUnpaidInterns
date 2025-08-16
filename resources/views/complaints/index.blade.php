<x-app-layout>
    <x-slot name="title">
        {{ __('Complaints & Feedback') }}
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ __('Complaints & Feedback') }}
                </h1>
                
                @if(auth()->user()->type === 'patient')
                    <a href="{{ route('complaints.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Submit Complaint') }}
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-6 bg-white dark:bg-zinc-800 shadow rounded-lg">
                @if($complaints->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    @if(auth()->user()->type !== 'patient')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                            {{ __('Patient') }}
                                        </th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('Created') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $complaint->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-300">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ ucfirst($complaint->category) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            <div class="max-w-xs truncate">
                                                {{ Str::limit($complaint->description, 50) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$complaint->status] }}">
                                                {{ $statusLabels[$complaint->status] }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->type !== 'patient')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $complaint->user->name }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-300">
                                            {{ $complaint->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('complaints.show', $complaint) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No complaints') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                            @if(auth()->user()->type === 'patient')
                                {{ __('Get started by submitting your first complaint.') }}
                            @else
                                {{ __('No complaints have been assigned to your department yet.') }}
                            @endif
                        </p>
                        @if(auth()->user()->type === 'patient')
                            <div class="mt-6">
                                <a href="{{ route('complaints.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('Submit Complaint') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
