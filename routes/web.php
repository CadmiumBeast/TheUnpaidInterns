<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Admin\AppointmentReservationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LoginController;

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

// Custom Login (NIC or Email) - only for guests
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});

// Patient Registration (separate from auth register)
Route::get('patient/register', [RegistrationController::class, 'showForm'])->name('patient.register');
Route::post('patient/register', [RegistrationController::class, 'register'])->name('patient.register.post');

// Public appointment browsing (no patient auth needed)
\Livewire\Volt\Volt::route('appointments', 'appointments.browse')->name('appointments.browse');

// Admin Routes
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Volt::route('admin', 'admin.dashboard')->name('admin.dashboard');

    // Doctor Management
    Volt::route('admin/doctors', 'admin.doctors.index')->name('admin.doctors.index');
    Volt::route('admin/doctors/create', 'admin.doctors.create')->name('admin.doctors.create');
    Volt::route('admin/doctors/{doctor}/edit', 'admin.doctors.edit')->name('admin.doctors.edit');

    // Admin schedules and appointments
    Volt::route('admin/schedules', 'admin.schedules.index')->name('admin.schedules.index');
    Volt::route('admin/schedules/{schedule}', 'admin.schedules.show')->name('admin.schedules.show');
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
    Volt::route('doctor/appointments/{appointment}', 'doctor.appointment-detail')->name('doctor.appointment');
});

// Patient Routes
Route::middleware(['auth', 'user-access:patient'])->group(function () {
    //
});

require __DIR__.'/auth.php';
