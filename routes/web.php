<?php

use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\ComplexController;
use App\Http\Controllers\Developer\DeveloperController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Import\ComplexImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\Property\PropertyDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');




    Route::get('/dashboard', [PropertyController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/properties/create',  [PropertyController::class,'create'])
        ->name('properties.create');

    // AJAX endpoint для DataTables
    Route::get('/properties/ajax-data', [PropertyController::class, 'ajaxData'])
        ->name('properties.ajax-data');


    Route::get('documents/{hash}/download', [PropertyDocumentController::class, 'download'])
        ->name('documents.download');
    Route::delete('documents/{hash}', [PropertyDocumentController::class, 'destroy'])
        ->name('documents.destroy');

    // Properties CRUD
    Route::resource('properties', PropertyController::class);

    // Property Photos AJAX endpoints
    Route::delete('/properties/{property}/photos/{photo}', [PropertyController::class, 'deletePhoto'])
        ->name('properties.photos.delete');
    Route::post('/properties/{property}/photos/reorder', [PropertyController::class, 'reorderPhotos'])
        ->name('properties.photos.reorder');


// Location routes (существующие)
    Route::prefix('location')->group(function () {
        Route::get('/search', [App\Http\Controllers\LocationController::class, 'search'])->name('location.search');
        Route::get('/street/{id}', [App\Http\Controllers\LocationController::class, 'show'])->name('location.show');

        // Регионы (новые routes)
        Route::get('/states/search', [App\Http\Controllers\LocationController::class, 'searchStates'])->name('location.states.search');
        Route::get('/states/default', [App\Http\Controllers\LocationController::class, 'getDefaultState'])->name('location.states.default');
        Route::get('/states/{id}', [App\Http\Controllers\LocationController::class, 'showState'])->name('location.states.show');

        // Данные для фильтра локаций
        Route::get('/filter-data', [App\Http\Controllers\LocationController::class, 'getFilterData'])->name('location.filter-data');

        // Универсальный поиск локации (страна/область/город)
        Route::get('/search-all', [App\Http\Controllers\LocationController::class, 'searchAll'])->name('location.search-all');
    });




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


// ========== Developers ==========
    Route::get('/developers/ajax-search', [DeveloperController::class, 'ajaxSearch'])
        ->name('developers.ajax-search');
    Route::get('/developers/ajax-data', [DeveloperController::class, 'ajaxData'])
        ->name('developers.ajax-data');
    Route::resource('developers', DeveloperController::class);

    // ========== Complexes ==========
    // AJAX для Select2 (должны быть ДО resource)
    Route::get('/complexes/search', [ComplexController::class, 'search'])->name('complexes.search');
    Route::get('/complexes/ajax-data', [ComplexController::class, 'ajaxData'])->name('complexes.ajax-data');
    Route::get('/blocks/search', [ComplexController::class, 'searchBlocks'])->name('blocks.search');
    Route::resource('complexes', ComplexController::class);

    // Импорт комплексов
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('complexes', [ComplexImportController::class, 'index'])->name('complexes.index');
        Route::post('complexes', [ComplexImportController::class, 'import'])->name('complexes.import');
    });


     Route::get('test', function () {
        return 'ok'; })->name('settings.index');

    // ========== Companies ==========
    Route::get('/companies/ajax-search', [CompanyController::class, 'ajaxSearch'])
        ->name('companies.ajax-search');
    Route::get('/companies/ajax-data', [CompanyController::class, 'ajaxData'])
        ->name('companies.ajax-data');
    Route::get('/companies/{company}/offices', [CompanyController::class, 'getOffices'])
        ->name('companies.offices');
    Route::resource('companies', CompanyController::class);

    // ========== Employees ==========
    Route::get('/employees/ajax-search', [EmployeeController::class, 'ajaxSearch'])
        ->name('employees.ajax-search');
    Route::get('/employees/ajax-data', [EmployeeController::class, 'ajaxData'])
        ->name('employees.ajax-data');
    Route::patch('/employees/{employee}/position', [EmployeeController::class, 'updatePosition'])
        ->name('employees.update-position');
    Route::patch('/employees/{employee}/office', [EmployeeController::class, 'updateOffice'])
        ->name('employees.update-office');
    Route::resource('employees', EmployeeController::class);

});

require __DIR__.'/auth.php';
