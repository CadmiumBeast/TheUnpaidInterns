{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background">
    {{-- Header --}}
    <header class="border-b bg-card shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    {{-- Icon --}}
                    <x-lucide-activity class="h-8 w-8 text-primary" />
                    <div>
                        <h1 class="text-xl font-bold text-foreground">OPD QR System</h1>
                        <p class="text-xs text-muted-foreground">Ministry of Health - Sri Lanka</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-medical-green text-white">
                    <x-lucide-check-circle class="h-3 w-3 mr-1" /> Government Approved
                </span>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="py-20 px-6 bg-gradient-to-br from-background via-hospital-gray to-background">
        <div class="max-w-7xl mx-auto text-center">
            <span class="mb-6 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary border border-primary/20">
                Sri Lankan Government Hospital System
            </span>

            <h1 class="text-4xl md:text-6xl font-bold text-foreground mb-6">
                Digital OPD Registration
                <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent block">
                    with QR Technology
                </span>
            </h1>

            <p class="text-xl text-muted-foreground mb-8 max-w-3xl mx-auto">
                Streamline patient registration in Sri Lankan government hospitals with our 
                advanced QR code-based system. Reduce wait times, improve efficiency, and 
                enhance patient experience across the healthcare network.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/dashboard') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium rounded-md text-white bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 shadow-medical">
                    Access Dashboard
                    <x-lucide-arrow-right class="ml-2 h-4 w-4" />
                </a>
                <a href="{{ url('/appointments') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium rounded-md border border-primary text-primary hover:bg-primary hover:text-primary-foreground">
                    <x-lucide-qr-code class="mr-2 h-4 w-4" />
                    View Appointments
                </a>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">
                    Modern Healthcare Registration
                </h2>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Designed specifically for Sri Lankan government hospitals to modernize 
                    patient registration and improve healthcare delivery.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $features = [
                        ['icon' => 'qr-code', 'title' => 'QR Code Integration', 'description' => 'Generate and scan QR codes for instant patient identification and seamless registration.'],
                        ['icon' => 'shield', 'title' => 'Secure & Compliant', 'description' => 'Built with healthcare data security in mind, ensuring patient privacy and regulatory compliance.'],
                        ['icon' => 'zap', 'title' => 'Fast Registration', 'description' => 'Streamlined registration process reduces wait times and improves patient experience.'],
                        ['icon' => 'users', 'title' => 'Multi-Hospital Support', 'description' => 'Designed for the Sri Lankan government hospital network with district-wide compatibility.']
                    ];
                @endphp

                @foreach($features as $feature)
                <div class="bg-card rounded-lg shadow-card hover:shadow-medical transition-shadow duration-300 p-6 text-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center mx-auto mb-4">
                        <x-dynamic-component :component="'lucide-' . $feature['icon']" class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-muted-foreground">{{ $feature['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Benefits Section --}}
    <section class="py-20 px-6 bg-hospital-gray">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-6">
                        Transforming Healthcare
                        <span class="text-primary block">in Sri Lanka</span>
                    </h2>
                    <p class="text-lg text-muted-foreground mb-8">
                        Our QR-based OPD registration system is revolutionizing how patients 
                        interact with government hospitals across Sri Lanka, making healthcare 
                        more accessible and efficient.
                    </p>

                    @php
                        $benefits = [
                            'Reduce patient wait times by up to 60%',
                            'Digital patient records management',
                            'Real-time registration tracking',
                            'Paperless registration system',
                            'Multi-language support (Sinhala, Tamil, English)',
                            'Integration with existing hospital systems'
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach($benefits as $benefit)
                        <div class="flex items-center space-x-3">
                            <x-lucide-check-circle class="h-5 w-5 text-medical-green flex-shrink-0" />
                            <span class="text-foreground">{{ $benefit }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Statistics Card --}}
                <div class="bg-card rounded-lg shadow-medical overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-secondary text-white p-4">
                        <h3 class="flex items-center space-x-2">
                            <x-lucide-activity class="h-5 w-5" />
                            <span>System Statistics</span>
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-primary">25+</p>
                            <p class="text-sm text-muted-foreground">Hospitals Connected</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-secondary">1,200+</p>
                            <p class="text-sm text-muted-foreground">Daily Registrations</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-medical-green">60%</p>
                            <p class="text-sm text-muted-foreground">Time Reduction</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-sri-lankan-orange">99.9%</p>
                            <p class="text-sm text-muted-foreground">System Uptime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t bg-card py-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <x-lucide-activity class="h-6 w-6 text-primary" />
                <h3 class="text-lg font-semibold text-foreground">OPD QR System</h3>
            </div>
            <p class="text-muted-foreground mb-4">
                Developed for the Ministry of Health, Sri Lanka
            </p>
            <p class="text-sm text-muted-foreground">
                © 2024 Government of Sri Lanka. All rights reserved.
            </p>
        </div>
    </footer>
</div>
@endsection
