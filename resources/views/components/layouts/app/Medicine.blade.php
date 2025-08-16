<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GovCare - Medicine Dashboard</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'teal': {
                                500: '#14b8a6',
                                600: '#0d9488',
                                700: '#0f766e'
                            },
                            'green': {
                                500: '#22c55e',
                                600: '#16a34a'
                            },
                            'blue': {
                                400: '#60a5fa',
                                500: '#3b82f6'
                            }
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
                <!-- Top Header -->
                <div class="flex justify-between items-center mb-4">
                    <!-- Logo and Branding -->
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/GovCare_Logo.png') }}" alt="GovCare Logo" class="w-10 h-10">
                        <div>
                            <div class="text-2xl font-bold text-teal-700">GovCare</div>
                            <div class="text-sm text-gray-600">OPD QR System</div>
                            <div class="text-xs text-gray-500">Ministry of Health - Sri Lanka</div>
                        </div>
                    </div>
                    
                    <!-- System Status -->
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">System Online</span>
                    </div>
                </div>
                
                <!-- Navigation Tabs -->
                <nav class="flex space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 transition-colors">
                        Overview
                    </a>
                    <a href="{{ route('register') }}" class="text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 transition-colors">
                        Register
                    </a>
                    <a href="{{ route('medicine') }}" class="text-teal-600 py-2 px-1 border-b-2 border-teal-600 font-medium">
                        Medicine
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Search and Filter Bar -->
            <div class="bg-gradient-to-r from-blue-500 to-teal-500 rounded-lg p-6 mb-8">
                <div class="flex flex-col lg:flex-row gap-4 items-center">
                
                    <!-- Date Filter -->
                    <div class="flex items-center space-x-2">
                        <label class="text-white text-sm font-medium">Date:</label>
                        <input type="date" 
                            value="2025-08-08"
                            class="bg-white text-gray-900 px-3 py-2 rounded-md text-sm border-0 focus:ring-2 focus:ring-white focus:ring-opacity-50 cursor-pointer hover:bg-gray-50 transition-colors">
                    </div>
                        
                    <!-- Pharmacy Filter -->
                    <div class="flex items-center space-x-2">
                        <label class="text-white text-sm font-medium">Pharmacy:</label>
                        <select id="pharmacy-filter"
                            class="bg-white text-gray-900 px-3 py-2 rounded-md text-sm border-0 focus:ring-2 focus:ring-white focus:ring-opacity-50"
                            onchange="filterMedicines()">
                            <option value="">All Pharmacies</option>
                            @foreach($pharmacies as $pharmacy)
                                <option value="{{ $pharmacy->name }}">{{ $pharmacy->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="medicine-search" placeholder="Search by Name, Company or ID..." 
                                class="w-full bg-white text-gray-900 px-4 py-2 pl-10 rounded-md text-sm border-0 focus:ring-2 focus:ring-white focus:ring-opacity-50"
                                oninput="filterMedicines()">
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    
                </div>
            </div>

            <!-- Medicine List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Available Medicines</h2>
                </div>
                <!-- Medicine Rows (Generated from $pharmacies) -->
                <div id="medicine-list" class="divide-y divide-gray-200">
                    @foreach ($pharmacies as $pharmacy )
                        @foreach($pharmacy->medicines as $medicine)
                            <div class="px-6 py-4 {{ $medicine->highlighted ? 'bg-purple-50 hover:bg-purple-100' : 'hover:bg-gray-50' }} transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $medicine->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $medicine->id }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-6">
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>{{ $pharmacy->name ?? '' }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $medicine->type }}</div>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                <span>{{ $pharmacy->phone_number ?? '' }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">{{ $medicine->status ?? 'Available' }}</span>
                                            <button class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                                Buy Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </main>

        <!-- JavaScript for Dynamic Medicine Loading -->
        <script>
            function filterMedicines() {
                const searchValue = document.getElementById('medicine-search').value.toLowerCase();
                const pharmacyValue = document.getElementById('pharmacy-filter').value.toLowerCase();
                document.querySelectorAll('#medicine-list > div').forEach(row => {
                    const name = row.querySelector('.font-medium.text-gray-900')?.textContent.toLowerCase() || '';
                    const company = row.querySelector('.text-xs.text-gray-500')?.textContent.toLowerCase() || '';
                    const id = row.querySelector('.text-sm.text-gray-500')?.textContent.toLowerCase() || '';
                    const pharmacy = row.querySelector('.flex.items-center.space-x-1 span')?.textContent.toLowerCase() || '';
                    const matchesSearch = searchValue === '' || name.includes(searchValue) || company.includes(searchValue) || id.includes(searchValue);
                    const matchesPharmacy = pharmacyValue === '' || pharmacy === pharmacyValue;
                    if (matchesSearch && matchesPharmacy) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>
    </body>
</html>