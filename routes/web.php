<?php

use App\Http\Controllers\Company\Company\CompanyController;
use App\Http\Controllers\Property\Complex\ComplexController;
use App\Http\Controllers\Property\Developer\DeveloperController;
use App\Http\Controllers\Company\Employee\EmployeeController;
use App\Http\Controllers\Import\ComplexImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Property\Property\PropertyController;
use App\Http\Controllers\Property\PropertyDocumentController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\LocationSettingsController;
use App\Http\Controllers\XmlFeedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Language switcher
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['uk', 'ru', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

// XML feed (public, no auth)
Route::get('/xml-feed/{adapter}', [XmlFeedController::class, 'show'])->name('xml-feed.show');

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
        Route::get('/search', [App\Http\Controllers\Location\LocationController::class, 'search'])->name('location.search');
        Route::get('/street/{id}', [App\Http\Controllers\Location\LocationController::class, 'show'])->name('location.show');

        // Регионы (новые routes)
        Route::get('/states/search', [App\Http\Controllers\Location\LocationController::class, 'searchStates'])->name('location.states.search');
        Route::get('/states/default', [App\Http\Controllers\Location\LocationController::class, 'getDefaultState'])->name('location.states.default');
        Route::get('/states/{id}', [App\Http\Controllers\Location\LocationController::class, 'showState'])->name('location.states.show');

        // Районы области по state_id
        Route::get('/regions/by-state', [App\Http\Controllers\Location\LocationController::class, 'getRegionsByState'])->name('location.regions.by-state');

        // Данные для фильтра локаций
        Route::get('/filter-data', [App\Http\Controllers\Location\LocationController::class, 'getFilterData'])->name('location.filter-data');

        // Универсальный поиск локации (страна/область/город)
        Route::get('/search-all', [App\Http\Controllers\Location\LocationController::class, 'searchAll'])->name('location.search-all');
    });




// ========== Contacts AJAX endpoints ==========
// Поиск контактов (для select2 / autocomplete)
    Route::get('/contacts/ajax-search', [App\Http\Controllers\Contact\ContactController::class, 'ajaxSearch'])
        ->name('contacts.ajax-search');

// Поиск контакта по номеру телефона
    Route::get('/contacts/ajax-search-by-phone', [App\Http\Controllers\Contact\ContactController::class, 'ajaxSearchByPhone'])
        ->name('contacts.ajax-search-by-phone');

// Создание контакта через AJAX (из модального окна)
    Route::post('/contacts/ajax-store', [App\Http\Controllers\Contact\ContactController::class, 'ajaxStore'])
        ->name('contacts.ajax-store');

// Получение данных контакта через AJAX
    Route::get('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxShow'])
        ->name('contacts.ajax-show');

// Обновление контакта через AJAX
    Route::put('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxUpdate'])
        ->name('contacts.ajax-update');

// Удаление контакта через AJAX
    Route::delete('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxDestroy'])
        ->name('contacts.ajax-destroy');

    // Привязать контакт к объекту
    Route::post('/properties/{property}/contacts', [App\Http\Controllers\Contact\ContactController::class, 'attachToProperty'])
        ->name('properties.contacts.attach');

// Отвязать контакт от объекта
    Route::delete('/properties/{property}/contacts/{contact}', [App\Http\Controllers\Contact\ContactController::class, 'detachFromProperty'])
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


    // ========== Settings ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        // Главная страница настроек (по умолчанию — Пользователи)
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Секции — та же страница, но с активной секцией
        Route::get('/users', [SettingsController::class, 'users'])->name('users.index');
        Route::get('/roles', [SettingsController::class, 'roles'])->name('roles.index');
        Route::get('/permissions', [SettingsController::class, 'permissions'])->name('permissions.index');

        // Roles CRUD
        Route::get('/roles/ajax-data', [RoleController::class, 'ajaxData'])->name('roles.ajax-data');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Users CRUD
        Route::get('/users/ajax-data', [UserController::class, 'ajaxData'])->name('users.ajax-data');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Permissions Matrix
        Route::get('/permissions/matrix', [PermissionController::class, 'matrix'])->name('permissions.matrix');
        Route::post('/permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
        Route::put('/permissions/role/{role}', [PermissionController::class, 'updateRole'])->name('permissions.update-role');
        Route::post('/permissions/bulk-update', [PermissionController::class, 'bulkUpdate'])->name('permissions.bulk-update');

        // Location sections
        Route::get('/countries', [SettingsController::class, 'countries'])->name('countries.index');
        Route::get('/regions', [SettingsController::class, 'regions'])->name('regions.index');
        Route::get('/oblast-regions', [SettingsController::class, 'oblastRegions'])->name('oblast-regions.index');
        Route::get('/cities', [SettingsController::class, 'cities'])->name('cities.index');
        Route::get('/districts', [SettingsController::class, 'districts'])->name('districts.index');
        Route::get('/zones', [SettingsController::class, 'zones'])->name('zones.index');
        Route::get('/streets', [SettingsController::class, 'streets'])->name('streets.index');

        // Location CRUD — AJAX helpers
        Route::get('/locations/regions-by-state', [LocationSettingsController::class, 'getRegions'])->name('locations.regions-by-state');
        Route::get('/locations/cities-by-state', [LocationSettingsController::class, 'getCities'])->name('locations.cities-by-state');
        Route::get('/locations/districts-by-city', [LocationSettingsController::class, 'getDistricts'])->name('locations.districts-by-city');
        Route::get('/locations/zones-by-city', [LocationSettingsController::class, 'getZones'])->name('locations.zones-by-city');

        // Countries CRUD
        Route::post('/countries', [LocationSettingsController::class, 'storeCountry'])->name('countries.store');
        Route::put('/countries/{country}', [LocationSettingsController::class, 'updateCountry'])->name('countries.update');
        Route::delete('/countries/{country}', [LocationSettingsController::class, 'destroyCountry'])->name('countries.destroy');

        // States CRUD
        Route::post('/states', [LocationSettingsController::class, 'storeState'])->name('states.store');
        Route::put('/states/{state}', [LocationSettingsController::class, 'updateState'])->name('states.update');
        Route::delete('/states/{state}', [LocationSettingsController::class, 'destroyState'])->name('states.destroy');

        // Regions (Районы области) CRUD
        Route::post('/regions', [LocationSettingsController::class, 'storeRegion'])->name('regions.store');
        Route::put('/regions/{region}', [LocationSettingsController::class, 'updateRegion'])->name('regions.update');
        Route::delete('/regions/{region}', [LocationSettingsController::class, 'destroyRegion'])->name('regions.destroy');

        // Districts CRUD
        Route::post('/districts', [LocationSettingsController::class, 'storeDistrict'])->name('districts.store');
        Route::put('/districts/{district}', [LocationSettingsController::class, 'updateDistrict'])->name('districts.update');
        Route::delete('/districts/{district}', [LocationSettingsController::class, 'destroyDistrict'])->name('districts.destroy');

        // Cities CRUD
        Route::post('/cities', [LocationSettingsController::class, 'storeCity'])->name('cities.store');
        Route::put('/cities/{city}', [LocationSettingsController::class, 'updateCity'])->name('cities.update');
        Route::delete('/cities/{city}', [LocationSettingsController::class, 'destroyCity'])->name('cities.destroy');

        // Zones CRUD
        Route::post('/zones', [LocationSettingsController::class, 'storeZone'])->name('zones.store');
        Route::put('/zones/{zone}', [LocationSettingsController::class, 'updateZone'])->name('zones.update');
        Route::delete('/zones/{zone}', [LocationSettingsController::class, 'destroyZone'])->name('zones.destroy');

        // Streets CRUD
        Route::post('/streets', [LocationSettingsController::class, 'storeStreet'])->name('streets.store');
        Route::put('/streets/{street}', [LocationSettingsController::class, 'updateStreet'])->name('streets.update');
        Route::delete('/streets/{street}', [LocationSettingsController::class, 'destroyStreet'])->name('streets.destroy');
    });

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
