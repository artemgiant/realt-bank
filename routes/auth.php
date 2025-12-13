<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'auth.livewire.register')
        ->name('register');

    Volt::route('login', 'auth.livewire.login')
        ->name('login');

    Volt::route('forgot-password', 'auth.livewire.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.livewire.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.livewire.verify-email')
        ->name('verification.notice');

    Volt::route('confirm-password', 'auth.livewire.confirm-password')
        ->name('password.confirm');
});
