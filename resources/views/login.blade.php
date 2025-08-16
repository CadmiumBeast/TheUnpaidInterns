<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GovCare - Login</title>
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
                <!-- Logo and Branding -->
                <div class="flex items-center space-x-3">
                    <img src="/images/logo.jpeg" alt="GovCare Logo" class="w-10 h-10">
                    <div>
                        <div class="text-2xl font-bold text-teal-700">GovCare</div>
                        <div class="text-sm text-gray-600">OPD QR System</div>
                        <div class="text-xs text-gray-500">Ministry of Health - Sri Lanka</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-md mx-auto">
                <!-- Login Card -->
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-teal-500 px-6 py-8 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
                        <p class="text-blue-100 mt-2">Sign in to your GovCare account</p>
                    </div>
                    
                    <!-- Login Form -->
                    <div class="px-6 py-8">
                        <form class="space-y-6">
                            <!-- NIC Field -->
                            <div>
                                <label for="nic" class="block text-sm font-medium text-gray-700 mb-2">
                                    National Identity Card (NIC)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="nic" 
                                        name="nic" 
                                        placeholder="Enter your NIC number"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        required
                                    >
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Format: 123456789V or 123456789012</p>
                            </div>
                            
                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Enter your password"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        required
                                    >
                                </div>
                            </div>
                            
                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input 
                                        id="remember" 
                                        name="remember" 
                                        type="checkbox" 
                                        class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                    >
                                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                                        Remember me
                                    </label>
                                </div>
                                <div class="text-sm">
                                    <a href="#" class="font-medium text-teal-600 hover:text-teal-500 transition-colors">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Login Button -->
                            <div>
                                <button 
                                    type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-teal-500 hover:from-blue-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 shadow-lg hover:shadow-xl"
                                >
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-blue-200 group-hover:text-blue-100 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                    </span>
                                    Sign In
                                </button>
                            </div>
                        </form>
                        
                        <!-- Divider -->
                        <div class="mt-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">New to GovCare?</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Register Link -->
                        <div class="mt-6 text-center">
                            <a href="{{ route('register') }}" class="font-medium text-teal-600 hover:text-teal-500 transition-colors">
                                Create a new account
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Having trouble signing in? 
                        <a href="#" class="font-medium text-teal-600 hover:text-teal-500 transition-colors">
                            Contact support
                        </a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-16">
            <div class="container mx-auto px-6 py-4">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; 2025 GovCare OPD QR System. Ministry of Health - Sri Lanka. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>

