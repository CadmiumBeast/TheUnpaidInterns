<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
<<<<<<< HEAD
use App\Http\Controllers\ComplaintController;
=======
use App\Http\Controllers\Admin\AppointmentReservationController;
>>>>>>> 621b53d6aa9e6236f83b9054b591e051bb64dfa4

Route::get('/', function () {
    return view('components.layouts.app.welcome');
})->name('home');

Route::get('/medicine', function () {
    return view('medicine');
})->name('medicine');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/login', function () {
    return view('login');
})->name('login');

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
<<<<<<< HEAD
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
=======
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
>>>>>>> 621b53d6aa9e6236f83b9054b591e051bb64dfa4
});

// Staff Routes
Route::middleware(['auth', 'user-access:staff'])->group(function () {
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
});

// Doctor Routes
Route::middleware(['auth', 'user-access:doctor'])->group(function () {
<<<<<<< HEAD
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
=======
    Volt::route('doctor', 'doctor.dashboard')->name('doctor.dashboard');
    Volt::route('doctor/schedules', 'doctor.schedules')->name('doctor.schedules');
>>>>>>> 621b53d6aa9e6236f83b9054b591e051bb64dfa4
});

// Patient Routes
Route::middleware(['auth', 'user-access:patient'])->group(function () {
<<<<<<< HEAD
    Route::resource('complaints', ComplaintController::class);
    Route::post('complaints/{complaint}/feedback', [ComplaintController::class, 'submitFeedback'])->name('complaints.feedback');
=======
    Route::get('medicine', [MedicineController::class, 'index'])->name('medicine');
    Volt::route('patient', 'patient.dashboard')->name('patient.dashboard');
    Route::get('patient/appointments', [AppointmentController::class, 'index'])->name('patient.appointments.index');
        Route::post('patient/appointments/reserve', [\App\Http\Controllers\AppointmentController::class, 'reserve'])->name('patient.appointments.reserve');
>>>>>>> 621b53d6aa9e6236f83b9054b591e051bb64dfa4
});

require __DIR__.'/auth.php';
