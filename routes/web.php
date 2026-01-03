<?php

use App\Http\Controllers\Import\ComplexImportController;
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

    // AJAX endpoint для DataTables
    Route::get('/properties/ajax-data', [PropertyController::class, 'ajaxData'])
        ->name('properties.ajax-data');




    // Properties CRUD
    Route::resource('properties', PropertyController::class);


    // Поиск улиц (для autocomplete)
    Route::get('/location/search', [App\Http\Controllers\LocationController::class, 'search'])
        ->name('location.search');

// Получение данных улицы по ID
    Route::get('/location/street/{id}', [App\Http\Controllers\LocationController::class, 'show'])
        ->name('location.show');





// ========== Contacts AJAX endpoints ==========
// Поиск контактов (для select2 / autocomplete)
    Route::get('/contacts/ajax-search', [App\Http\Controllers\ContactController::class, 'ajaxSearch'])
        ->name('contacts.ajax-search');

// Поиск контакта по номеру телефона
    Route::get('/contacts/ajax-search-by-phone', [App\Http\Controllers\ContactController::class, 'ajaxSearchByPhone'])
        ->name('contacts.ajax-search-by-phone');

// Создание контакта через AJAX (из модального окна)
    Route::post('/contacts/ajax-store', [App\Http\Controllers\ContactController::class, 'ajaxStore'])
        ->name('contacts.ajax-store');

// Получение данных контакта через AJAX
    Route::get('/contacts/{contact}/ajax', [App\Http\Controllers\ContactController::class, 'ajaxShow'])
        ->name('contacts.ajax-show');

// Обновление контакта через AJAX
    Route::put('/contacts/{contact}/ajax', [App\Http\Controllers\ContactController::class, 'ajaxUpdate'])
        ->name('contacts.ajax-update');

// Удаление контакта через AJAX
    Route::delete('/contacts/{contact}/ajax', [App\Http\Controllers\ContactController::class, 'ajaxDestroy'])
        ->name('contacts.ajax-destroy');

    // Привязать контакт к объекту
    Route::post('/properties/{property}/contacts', [App\Http\Controllers\ContactController::class, 'attachToProperty'])
        ->name('properties.contacts.attach');

// Отвязать контакт от объекта
    Route::delete('/properties/{property}/contacts/{contact}', [App\Http\Controllers\ContactController::class, 'detachFromProperty'])
        ->name('properties.contacts.detach');




    // Импорт комплексов
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('complexes', [ComplexImportController::class, 'index'])->name('complexes.index');
        Route::post('complexes', [ComplexImportController::class, 'import'])->name('complexes.import');
    });


});

require __DIR__.'/auth.php';
