<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('components.layouts.app.welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
}
);

Route::get('medicine', function () {
    return view('components.layouts.app.medicine');
})->name('medicine');

// Admin Routes
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Volt::route('admin', 'admin.dashboard')->name('admin.dashboard');

    // Doctor Management
    Volt::route('admin/doctors', 'admin.doctors.index')->name('admin.doctors.index');
    Volt::route('admin/doctors/create', 'admin.doctors.create')->name('admin.doctors.create');
    Volt::route('admin/doctors/{doctor}/edit', 'admin.doctors.edit')->name('admin.doctors.edit');

    // Admin schedules and appointments
    Volt::route('admin/schedules', 'admin.schedules.index')->name('admin.schedules.index');
    Volt::route('admin/appointments', 'admin.appointments.index')->name('admin.appointments.index');
});

// Staff Routes
Route::middleware(['auth', 'user-access:staff'])->group(function () {
    //
});

// Doctor Routes
Route::middleware(['auth', 'user-access:doctor'])->group(function () {
    Volt::route('doctor', 'doctor.dashboard')->name('doctor.dashboard');
    Volt::route('doctor/schedules', 'doctor.schedules')->name('doctor.schedules');
});

// Patient Routes
Route::middleware(['auth', 'user-access:patient'])->group(function () {
    //
});

require __DIR__.'/auth.php';
