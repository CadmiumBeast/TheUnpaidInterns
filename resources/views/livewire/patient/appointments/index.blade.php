

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GovCare - Book Appointment</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'teal': { 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e' },
                        'green': { 500: '#22c55e', 600: '#16a34a' },
                        'blue': { 400: '#60a5fa', 500: '#3b82f6' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    <!-- Header Section -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center mb-4">
                <!-- Logo and Branding -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/GovCare_Logo_Small.png') }}" alt="GovCare Logo" width="200px" height="auto">
                </div>
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit" class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow transition-colors">
                        <svg class="w-4 h-4 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
            <!-- Navigation Tabs -->
            <nav class="flex justify-center mt-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex space-x-2 px-2 py-1">
                    <a href="{{ route('patient.appointments.index') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('patient.appointments.*') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                        <svg class="w-4 h-4 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                        Appointments
                    </a>
                    <a href="{{ route('medicine') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('medicine') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                        Medicine
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-4">
        <h1 class="text-2xl font-bold mb-6">Book an Appointment</h1>
        <!-- Filter Form -->
        <form method="GET" action="{{ route('patient.appointments.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-lg shadow">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="specialty" class="block text-sm font-medium text-gray-700">Specialty</label>
                <select name="specialty" id="specialty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All</option>
                    @foreach(config('specialties.list') as $spec)
                        <option value="{{ $spec }}" {{ request('specialty') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="doctor" class="block text-sm font-medium text-gray-700">Doctor</label>
                <select name="doctor" id="doctor" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All</option>
                    @isset($doctorOptions)
                        @foreach($doctorOptions as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor') == $doc->id ? 'selected' : '' }}>{{ $doc->full_name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Doctor, hospital..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="md:col-span-4 flex justify-end items-end">
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg shadow hover:bg-teal-700">Filter</button>
            </div>
        </form>

        <!-- Available Schedules -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @isset($cards)
            @forelse($cards as $card)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <img src="{{ $card->doctor->profile_photo_path ? asset($card->doctor->profile_photo_path) : asset('images/default-doctor.png') }}" alt="Doctor Photo" class="w-12 h-12 rounded-full mr-3">
                            <div>
                                <div class="font-bold text-lg">{{ $card->doctor->full_name }}</div>
                                <div class="text-sm text-gray-600">{{ $card->doctor->specialty }}</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 mb-2">Hospital: {{ $card->hospital_name }}</div>
                        <div class="text-sm text-gray-700 mb-2">Date: {{ $card->date }}</div>
                        <div class="text-sm text-gray-700 mb-2">Time: {{ $card->start_time }} - {{ $card->end_time }}</div>
                        <div class="text-sm text-gray-700 mb-2">Capacity: {{ $card->capacity }}</div>
                    </div>
                    <form method="POST" action="{{ route('patient.appointments.reserve') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $card->doctor->id }}">
                        <input type="hidden" name="schedule_id" value="{{ $card->id }}">
                        <input type="hidden" name="date" value="{{ $card->date }}">
                        <input type="hidden" name="start_time" value="{{ $card->start_time }}">
                        <input type="hidden" name="patient_id" value="{{ auth()->user()->id }}">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Book Appointment</button>
                    </form>
                </div>
            @empty
                <div class="col-span-2 text-center text-gray-500 py-12">No available schedules found for the selected filters.</div>
            @endforelse
            @endisset
        </div>
    </main>
</body>
</html>
