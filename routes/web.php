<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\AppointmentReservationController;

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



// Public appointment browsing (no patient auth needed)
    Volt::route('appointments', 'appointments.browse')->name('appointments.browse');

// Admin Routes
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Volt::route('admin', 'admin.dashboard')->name('admin.dashboard');

    // Doctor Management
    Volt::route('admin/doctors', 'admin.doctors.index')->name('admin.doctors.index');
    Volt::route('admin/doctors/create', 'admin.doctors.create')->name('admin.doctors.create');
    Volt::route('admin/doctors/{doctor}/edit', 'admin.doctors.edit')->name('admin.doctors.edit');
    Route::get('admin/medicine', [MedicineController::class, 'index'])->name('admin.medicine');
    // Admin schedules and appointments
    Volt::route('admin/schedules', 'admin.schedules.index')->name('admin.schedules.index');
    Volt::route('admin/appointments', 'admin.appointments.index')->name('admin.appointments.index');
    Route::post('admin/appointments/reserve', [AppointmentReservationController::class, 'store'])->name('admin.appointments.reserve');
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
    Route::get('medicine', [MedicineController::class, 'index'])->name('medicine');
    Volt::route('patient', 'patient.dashboard')->name('patient.dashboard');
    Route::get('patient/appointments', [AppointmentController::class, 'index'])->name('patient.appointments.index');
        Route::post('patient/appointments/reserve', [\App\Http\Controllers\AppointmentController::class, 'reserve'])->name('patient.appointments.reserve');
});

require __DIR__.'/auth.php';
