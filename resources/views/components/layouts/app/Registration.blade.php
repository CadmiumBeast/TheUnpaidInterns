<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GovCare - Patient Registration</title>
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
                    <a href="{{ route('patient.register') }}" class="text-teal-600 py-2 px-1 border-b-2 border-teal-600 font-medium bg-gray-100 rounded-t-lg px-4">
                        Register
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 transition-colors">
                        Scan QR
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 p-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 p-3">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Page Header -->
            <div class="bg-gradient-to-r from-blue-500 to-teal-500 rounded-lg p-6 mb-8 text-white">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Patient Registration</h1>
                        <p class="text-blue-100">Register a new patient for OPD services</p>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <form class="max-w-4xl mx-auto" action="{{ route('patient.register.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Personal Information Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="nic" class="block text-sm font-medium text-gray-700 mb-2">NIC number</label>
                            <input type="text" id="nic" name="nic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" id="dob" name="dob" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="male" checked 
                                           class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-2 text-sm text-gray-700">Male</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="female" 
                                           class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-2 text-sm text-gray-700">Female</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="other" 
                                           class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                    <span class="ml-2 text-sm text-gray-700">Other</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Contact Information</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone number</label>
                            <input type="tel" id="phone" name="phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                            <input type="tel" id="emergency_contact" name="emergency_contact" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Address Information</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-2">District</label>
                            <select id="district" name="district" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Select District</option>
                                <option value="colombo">Colombo</option>
                                <option value="gampaha">Gampaha</option>
                                <option value="kalutara">Kalutara</option>
                                <option value="kandy">Kandy</option>
                                <option value="matale">Matale</option>
                                <option value="nuwara_eliya">Nuwara Eliya</option>
                                <option value="galle">Galle</option>
                                <option value="matara">Matara</option>
                                <option value="hambantota">Hambantota</option>
                                <option value="jaffna">Jaffna</option>
                                <option value="kilinochchi">Kilinochchi</option>
                                <option value="mullaitivu">Mullaitivu</option>
                                <option value="vavuniya">Vavuniya</option>
                                <option value="trincomalee">Trincomalee</option>
                                <option value="batticaloa">Batticaloa</option>
                                <option value="ampara">Ampara</option>
                                <option value="polonnaruwa">Polonnaruwa</option>
                                <option value="anuradhapura">Anuradhapura</option>
                                <option value="kurunegala">Kurunegala</option>
                                <option value="puttalam">Puttalam</option>
                                <option value="badulla">Badulla</option>
                                <option value="monaragala">Monaragala</option>
                                <option value="ratnapura">Ratnapura</option>
                                <option value="kegalle">Kegalle</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="hospital" class="block text-sm font-medium text-gray-700 mb-2">Hospital</label>
                            <select id="hospital" name="hospital" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Select Hospital</option>
                                <option value="national_hospital">National Hospital of Sri Lanka</option>
                                <option value="colombo_north">Colombo North Teaching Hospital</option>
                                <option value="colombo_south">Colombo South Teaching Hospital</option>
                                <option value="dehiwala">Dehiwala General Hospital</option>
                                <option value="kalubowila">Kalubowila Teaching Hospital</option>
                                <option value="kandy">Kandy Teaching Hospital</option>
                                <option value="peradeniya">Peradeniya Teaching Hospital</option>
                                <option value="galle">Galle General Hospital</option>
                                <option value="matara">Matara General Hospital</option>
                                <option value="jaffna">Jaffna Teaching Hospital</option>
                                <option value="anuradhapura">Anuradhapura Teaching Hospital</option>
                                <option value="kurunegala">Kurunegala Teaching Hospital</option>
                                <option value="badulla">Badulla General Hospital</option>
                                <option value="ratnapura">Ratnapura General Hospital</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-500 to-teal-500 text-white px-8 py-3 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-teal-600 transition-all duration-200 transform hover:scale-105">
                        Register Patient
                    </button>
                </div>
            </form>
        </main>
    </body>
</html>