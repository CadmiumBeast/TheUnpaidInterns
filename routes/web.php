<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ComplaintController;

Route::get('/', function () {
    return view('welcome');
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


// Admin Routes
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
});

// Staff Routes
Route::middleware(['auth', 'user-access:staff'])->group(function () {
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
});

// Doctor Routes
Route::middleware(['auth', 'user-access:doctor'])->group(function () {
    Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    Route::post('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
});

// Patient Routes
Route::middleware(['auth', 'user-access:patient'])->group(function () {
    Route::resource('complaints', ComplaintController::class);
    Route::post('complaints/{complaint}/feedback', [ComplaintController::class, 'submitFeedback'])->name('complaints.feedback');
});

require __DIR__.'/auth.php';
