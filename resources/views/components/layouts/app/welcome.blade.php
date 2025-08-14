<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GovCare - OPD-QR System</title>
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
    <body class="bg-white text-gray-900 font-sans">
        <!-- Header/Hero Section -->
        <header class="relative bg-gradient-to-br from-blue-50 via-white to-teal-50 py-12">
            <div class="container mx-auto px-4">
                <!-- Top Navigation -->
                <nav class="flex justify-between items-center mb-8">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/GovCare_Logo.png') }}" alt="GovCare Logo" class="w-10 h-10">
                        <div>
                            <div class="text-xl font-bold text-gray-900">GovCare</div>
                            <div class="text-sm text-gray-600">OPD-QR System</div>
                        </div>
                    </div>
                    
                    <!-- Government Approved Badge -->
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Government Approved
                    </div>
                </nav>
                
                <!-- Hero Content -->
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Sri Lankan Government Hospital System Badge -->
                    <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                        Sri Lankan Government Hospital System
                    </div>
                    
                    <!-- Main Title -->
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                        Digital OPD Registration with 
                        <span class="text-teal-600">QR Technology</span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Streamline patient registration in Sri Lankan government hospitals with our advanced QR code-based system. Reduce wait times, improve efficiency, and enhance patient experience across the healthcare network.
                    </p>
                    
                    <!-- Call-to-Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('dashboard') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors duration-200">
                            Access Dashboard →
                        </a>
                        <a href="{{ route('medicine') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>View Medicine</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Modern Healthcare Registration Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Modern Healthcare Registration
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Designed specifically for Sri Lankan government hospitals to modernize patient registration and improve healthcare delivery.
                    </p>
                </div>
                
                <!-- Feature Blocks -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- QR Code Integration -->
                    <div class="text-center p-6 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">QR Code Integration</h3>
                        <p class="text-gray-600">Generate and scan QR codes for instant patient identification and seamless registration.</p>
                    </div>
                    
                    <!-- Secure & Compliant -->
                    <div class="text-center p-6 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure & Compliant</h3>
                        <p class="text-gray-600">Built with healthcare data security in mind ensuring patient privacy and regulatory compliance.</p>
                    </div>
                    
                    <!-- Fast Registration -->
                    <div class="text-center p-6 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Registration</h3>
                        <p class="text-gray-600">Streamlined registration process reduces wait times and improves patient experience.</p>
                    </div>
                    
                    <!-- Multi-Hospital Support -->
                    <div class="text-center p-6 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Multi-Hospital Support</h3>
                        <p class="text-gray-600">Designed for the Sri Lankan government hospital network with district-wide compatibility.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Transforming Healthcare in Sri Lanka Section -->
        <section class="py-16 bg-gradient-to-r from-teal-50 to-blue-50">
            <div class="container mx-auto px-4">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                            Transforming Healthcare in Sri Lanka
                        </h2>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            Our QR-based OPD-registration system is revolutionizing how patients interact with government hospitals across Sri Lanka, making healthcare more accessible and efficient.
                        </p>
                        
                        <!-- Benefits List -->
                        <ul class="space-y-4">
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Reduce patient wait times by up to 50%</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Digital patient records management</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Real-time registration tracking</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                <span class="text-gray-700">Paperless registration system</span>
                        </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                <span class="text-gray-700">Multi-language support (Sinhala, Tamil, English)</span>
                        </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Integration with existing hospital systems</span>
                        </li>
                    </ul>
                </div>
                    
                    <!-- Right Content - System Statistics -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-6 py-4 rounded-t-lg -mt-8 -mx-8 mb-6">
                            <h3 class="text-xl font-bold">System Statistics</h3>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Hospitals connected</span>
                                <span class="text-2xl font-bold text-teal-600">25+</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Daily registrations</span>
                                <span class="text-2xl font-bold text-teal-600">1,200+</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Wait time reduction</span>
                                <span class="text-2xl font-bold text-teal-600">60%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">System uptime</span>
                                <span class="text-2xl font-bold text-teal-600">99.9%</span>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4 text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <img src="{{ asset('images/GovCare_Logo.png') }}" alt="GovCare Logo" class="w-1/4 h-1/4">
                </div>
                <p class="text-gray-400 mb-2">Developed for the Ministry of Health, Sri Lanka</p>
                <p class="text-gray-500">© 2025 Government of Sri Lanka. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>