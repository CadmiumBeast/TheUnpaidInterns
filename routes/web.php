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
    //
});

// Staff Routes
Route::middleware(['auth', 'user-access:staff'])->group(function () {
    //
});

// Doctor Routes
Route::middleware(['auth', 'user-access:doctor'])->group(function () {
    //
});

// Patient Routes
Route::middleware(['auth', 'user-access:patient'])->group(function () {
    //
});

require __DIR__.'/auth.php';
