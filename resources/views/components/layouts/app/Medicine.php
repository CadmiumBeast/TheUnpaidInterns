{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background">
  {{-- Header --}}
  <header class="border-b bg-card shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          {{-- Activity icon --}}
          <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          <div>
            <h1 class="text-xl font-bold text-foreground">OPD QR System</h1>
            <p class="text-xs text-muted-foreground">Ministry of Health - Sri Lanka</p>
          </div>
        </div>
        <span class="bg-medical-green text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
          <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M9 12l2 2l4 -4" />
            <circle cx="12" cy="12" r="10" />
          </svg>
          Government Approved
        </span>
      </div>
    </div>
  </header>

  {{-- Hero --}}
  <section class="py-20 px-6 bg-gradient-to-br from-background via-hospital-gray to-background text-center">
    <div class="max-w-7xl mx-auto">
      <span class="mb-6 inline-block bg-primary/10 text-primary border border-primary/20 text-sm px-3 py-1 rounded-full">
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
        <a href="{{ url('/dashboard') }}" class="bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 shadow-medical text-white px-6 py-3 rounded-lg flex items-center justify-center">
          Access Dashboard
          <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </a>
        <a href="{{ url('/appointments') }}" class="border border-primary text-primary hover:bg-primary hover:text-primary-foreground px-6 py-3 rounded-lg flex items-center justify-center">
          <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h4v4H3zM17 3h4v4h-4zM3 17h4v4H3zM17 17h4v4h-4z" />
            <path d="M7 7h10v10H7z" />
          </svg>
          View Appointments
        </a>
      </div>
    </div>
  </section>

  {{-- Features --}}
  <section class="py-20 px-6">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">Modern Healthcare Registration</h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
          Designed specifically for Sri Lankan government hospitals to modernize 
          patient registration and improve healthcare delivery.
        </p>
      </div>
      @php
        $features = [
          ['icon' => '<path d="M3 3h4v4H3zM17 3h4v4h-4zM3 17h4v4H3zM17 17h4v4h-4z" /><path d="M7 7h10v10H7z" />', 'title' => 'QR Code Integration', 'description' => 'Generate and scan QR codes for instant patient identification and seamless registration.'],
          ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />', 'title' => 'Secure & Compliant', 'description' => 'Built with healthcare data security in mind, ensuring patient privacy and regulatory compliance.'],
          ['icon' => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />', 'title' => 'Fast Registration', 'description' => 'Streamlined registration process reduces wait times and improves patient experience.'],
          ['icon' => '<circle cx="9" cy="7" r="4" /><path d="M17 11a4 4 0 1 0 0-8" /><path d="M2 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" /><path d="M14 21v-2a4 4 0 0 1 3-3.87" />', 'title' => 'Multi-Hospital Support', 'description' => 'Designed for the Sri Lankan government hospital network with district-wide compatibility.'],
        ];
      @endphp
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($features as $f)
        <div class="shadow-card hover:shadow-medical transition-shadow duration-300 bg-white rounded-lg text-center p-6">
          <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center mx-auto mb-4">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
          </div>
          <h3 class="text-lg font-semibold">{{ $f['title'] }}</h3>
          <p class="text-muted-foreground mt-2">{{ $f['description'] }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Benefits --}}
  <section class="py-20 px-6 bg-hospital-gray">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
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
            'Integration with existing hospital systems',
          ];
        @endphp
        <div class="space-y-4">
          @foreach($benefits as $b)
          <div class="flex items-center space-x-3">
            <svg class="h-5 w-5 text-medical-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M9 12l2 2l4 -4" />
              <circle cx="12" cy="12" r="10" />
            </svg>
            <span>{{ $b }}</span>
          </div>
          @endforeach
        </div>
      </div>
      <div class="shadow-medical rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-secondary text-white p-4 flex items-center space-x-2">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          <span>System Statistics</span>
        </div>
        <div class="p-6 grid grid-cols-2 gap-6 text-center">
          <div>
            <p class="text-3xl font-bold text-primary">25+</p>
            <p class="text-sm text-muted-foreground">Hospitals Connected</p>
          </div>
          <div>
            <p class="text-3xl font-bold text-secondary">1,200+</p>
            <p class="text-sm text-muted-foreground">Daily Registrations</p>
          </div>
          <div>
            <p class="text-3xl font-bold text-medical-green">60%</p>
            <p class="text-sm text-muted-foreground">Time Reduction</p>
          </div>
          <div>
            <p class="text-3xl font-bold text-sri-lankan-orange">99.9%</p>
            <p class="text-sm text-muted-foreground">System Uptime</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Footer --}}
  <footer class="border-t bg-card py-12 px-6 text-center">
    <div class="flex items-center justify-center space-x-3 mb-4">
      <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
      <h3 class="text-lg font-semibold text-foreground">OPD QR System</h3>
    </div>
    <p class="text-muted-foreground mb-4">Developed for the Ministry of Health, Sri Lanka</p>
    <p class="text-sm text-muted-foreground">© 2024 Government of Sri Lanka. All rights reserved.</p>
  </footer>
</div>
@endsection
