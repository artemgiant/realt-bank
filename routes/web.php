<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');




    Route::get('/dashboard', 'App\Http\Controllers\PropertyController@index')->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/properties/create', 'App\Http\Controllers\PropertyController@create')
        ->name('properties.create');

    // Properties CRUD
    Route::resource('properties', PropertyController::class);

});

require __DIR__.'/auth.php';
