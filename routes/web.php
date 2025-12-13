<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/properties', function () {
        return view('pages.properties.index');
    })->name('properties.index');

    Route::get('/profile', function () {
        return view('pages.profile.index');
    })->name('profile.index');

});

require __DIR__.'/auth.php';


// CRM Routes (потребують авторизації)
Route::middleware(['auth', 'verified'])->group(function () {

    // Недвижимость
    Route::get('/properties', function () {
        return view('pages.properties.index');
    })->name('properties.index');

    // Комплекси (заглушка)
    Route::get('/complexes', function () {
        return view('pages.properties.index'); // тимчасово
    })->name('complexes.index');

    // Девелопери (заглушка)
    Route::get('/developers', function () {
        return view('pages.properties.index'); // тимчасово
    })->name('developers.index');

    // Угоди
    Route::get('/deals', function () {
        return view('pages.properties.index'); // тимчасово
    })->name('deals.index');

    // Задачі
    Route::get('/tasks', function () {
        return view('pages.properties.index'); // тимчасово
    })->name('tasks.index');

    // Агентство
    Route::get('/agency', function () {
        return view('pages.properties.index'); // тимчасово
    })->name('agency.index');

});

// Редірект dashboard на properties
Route::redirect('/dashboard', '/properties');








require __DIR__.'/auth.php';
