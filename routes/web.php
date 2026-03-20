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

    // ========== Properties ==========
    // Create routes BEFORE view (properties.create) — /create must precede /{property}
    Route::middleware('permission:properties.create')->group(function () {
        Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    });

    // Edit routes (properties.edit)
    Route::middleware('permission:properties.edit')->group(function () {
        Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::patch('/properties/{property}', [PropertyController::class, 'update']);
        Route::delete('/properties/{property}/photos/{photo}', [PropertyController::class, 'deletePhoto'])
            ->name('properties.photos.delete');
        Route::post('/properties/{property}/photos/reorder', [PropertyController::class, 'reorderPhotos'])
            ->name('properties.photos.reorder');
        Route::post('/properties/{property}/refresh-updated', [PropertyController::class, 'refreshUpdatedAt'])
            ->name('properties.refresh-updated');
    });

    // Delete routes (properties.delete)
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])
        ->middleware('permission:properties.delete')
        ->name('properties.destroy');

    // View routes (properties.view)
    Route::middleware('permission:properties.view')->group(function () {
        Route::get('/properties/ajax-data', [PropertyController::class, 'ajaxData'])
            ->name('properties.ajax-data');
        Route::get('/properties/check-duplicate-address', [PropertyController::class, 'checkDuplicateAddress'])
            ->name('properties.check-duplicate-address');
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    });

    // Documents (tied to properties.view for download, properties.edit for delete)
    Route::get('documents/{hash}/download', [PropertyDocumentController::class, 'download'])
        ->middleware('permission:properties.view')
        ->name('documents.download');
    Route::delete('documents/{hash}', [PropertyDocumentController::class, 'destroy'])
        ->middleware('permission:properties.edit')
        ->name('documents.destroy');

    // ========== Location routes (public for authenticated users — used in property forms) ==========
    Route::prefix('location')->group(function () {
        Route::get('/search', [App\Http\Controllers\Location\LocationController::class, 'search'])->name('location.search');
        Route::get('/street/{id}', [App\Http\Controllers\Location\LocationController::class, 'show'])->name('location.show');
        Route::get('/states/search', [App\Http\Controllers\Location\LocationController::class, 'searchStates'])->name('location.states.search');
        Route::get('/states/default', [App\Http\Controllers\Location\LocationController::class, 'getDefaultState'])->name('location.states.default');
        Route::get('/states/{id}', [App\Http\Controllers\Location\LocationController::class, 'showState'])->name('location.states.show');
        Route::get('/regions/by-state', [App\Http\Controllers\Location\LocationController::class, 'getRegionsByState'])->name('location.regions.by-state');
        Route::get('/filter-data', [App\Http\Controllers\Location\LocationController::class, 'getFilterData'])->name('location.filter-data');
        Route::get('/search-all', [App\Http\Controllers\Location\LocationController::class, 'searchAll'])->name('location.search-all');
    });

    // ========== Contacts (без permission — контакты пока без отдельных прав) ==========
    Route::get('/contacts/ajax-search', [App\Http\Controllers\Contact\ContactController::class, 'ajaxSearch'])
        ->name('contacts.ajax-search');
    Route::get('/contacts/ajax-search-by-phone', [App\Http\Controllers\Contact\ContactController::class, 'ajaxSearchByPhone'])
        ->name('contacts.ajax-search-by-phone');
    Route::post('/contacts/ajax-store', [App\Http\Controllers\Contact\ContactController::class, 'ajaxStore'])
        ->name('contacts.ajax-store');
    Route::get('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxShow'])
        ->name('contacts.ajax-show');
    Route::put('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxUpdate'])
        ->name('contacts.ajax-update');
    Route::delete('/contacts/{contact}/ajax', [App\Http\Controllers\Contact\ContactController::class, 'ajaxDestroy'])
        ->name('contacts.ajax-destroy');
    Route::post('/properties/{property}/contacts', [App\Http\Controllers\Contact\ContactController::class, 'attachToProperty'])
        ->name('properties.contacts.attach');
    Route::delete('/properties/{property}/contacts/{contact}', [App\Http\Controllers\Contact\ContactController::class, 'detachFromProperty'])
        ->name('properties.contacts.detach');

    // ========== Developers ==========
    // CRUD routes BEFORE view — /create must precede /{developer}
    Route::middleware('permission:developers.create')->group(function () {
        Route::get('/developers/create', [DeveloperController::class, 'create'])->name('developers.create');
        Route::post('/developers', [DeveloperController::class, 'store'])->name('developers.store');
    });
    Route::middleware('permission:developers.edit')->group(function () {
        Route::get('/developers/{developer}/edit', [DeveloperController::class, 'edit'])->name('developers.edit');
        Route::put('/developers/{developer}', [DeveloperController::class, 'update'])->name('developers.update');
        Route::patch('/developers/{developer}', [DeveloperController::class, 'update']);
    });
    Route::middleware('permission:developers.delete')->group(function () {
        Route::delete('/developers/{developer}', [DeveloperController::class, 'destroy'])->name('developers.destroy');
    });

    // View routes (developers.view)
    Route::middleware('permission:developers.view')->group(function () {
        Route::get('/developers/ajax-search', [DeveloperController::class, 'ajaxSearch'])
            ->name('developers.ajax-search');
        Route::get('/developers/ajax-data', [DeveloperController::class, 'ajaxData'])
            ->name('developers.ajax-data');
        Route::get('/developers', [DeveloperController::class, 'index'])->name('developers.index');
        Route::get('/developers/{developer}', [DeveloperController::class, 'show'])->name('developers.show');
    });

    // ========== Complexes ==========
    // CRUD routes BEFORE view — /create must precede /{complex}
    Route::middleware('permission:complexes.create')->group(function () {
        Route::get('/complexes/create', [ComplexController::class, 'create'])->name('complexes.create');
        Route::post('/complexes', [ComplexController::class, 'store'])->name('complexes.store');
    });
    Route::middleware('permission:complexes.edit')->group(function () {
        Route::get('/complexes/{complex}/edit', [ComplexController::class, 'edit'])->name('complexes.edit');
        Route::put('/complexes/{complex}', [ComplexController::class, 'update'])->name('complexes.update');
        Route::patch('/complexes/{complex}', [ComplexController::class, 'update']);
    });
    Route::middleware('permission:complexes.delete')->group(function () {
        Route::delete('/complexes/{complex}', [ComplexController::class, 'destroy'])->name('complexes.destroy');
    });

    // View routes (complexes.view)
    Route::middleware('permission:complexes.view')->group(function () {
        Route::get('/complexes/search', [ComplexController::class, 'search'])->name('complexes.search');
        Route::get('/complexes/ajax-data', [ComplexController::class, 'ajaxData'])->name('complexes.ajax-data');
        Route::get('/blocks/search', [ComplexController::class, 'searchBlocks'])->name('blocks.search');
        Route::get('/complexes', [ComplexController::class, 'index'])->name('complexes.index');
        Route::get('/complexes/{complex}', [ComplexController::class, 'show'])->name('complexes.show');
    });

    // Импорт комплексов (complexes.create)
    Route::middleware('permission:complexes.create')->prefix('import')->name('import.')->group(function () {
        Route::get('complexes', [ComplexImportController::class, 'index'])->name('complexes.index');
        Route::post('complexes', [ComplexImportController::class, 'import'])->name('complexes.import');
    });

    // ========== Companies ==========
    // CRUD routes BEFORE view — /create must precede /{company}
    Route::middleware('permission:companies.create')->group(function () {
        Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    });
    Route::middleware('permission:companies.edit')->group(function () {
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}', [CompanyController::class, 'update']);
    });
    Route::middleware('permission:companies.delete')->group(function () {
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    });

    // View routes (companies.view)
    Route::middleware('permission:companies.view')->group(function () {
        Route::get('/companies/ajax-search', [CompanyController::class, 'ajaxSearch'])
            ->name('companies.ajax-search');
        Route::get('/companies/ajax-data', [CompanyController::class, 'ajaxData'])
            ->name('companies.ajax-data');
        Route::get('/companies/{company}/offices', [CompanyController::class, 'getOffices'])
            ->name('companies.offices');
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    });

    // ========== Employees ==========
    // CRUD routes BEFORE view — /create must precede /{employee}
    Route::middleware('permission:employees.create')->group(function () {
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    });
    Route::middleware('permission:employees.edit')->group(function () {
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{employee}', [EmployeeController::class, 'update']);
        Route::patch('/employees/{employee}/position', [EmployeeController::class, 'updatePosition'])
            ->name('employees.update-position');
        Route::patch('/employees/{employee}/office', [EmployeeController::class, 'updateOffice'])
            ->name('employees.update-office');
    });
    Route::middleware('permission:employees.delete')->group(function () {
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // View routes (employees.view)
    Route::middleware('permission:employees.view')->group(function () {
        Route::get('/employees/ajax-search', [EmployeeController::class, 'ajaxSearch'])
            ->name('employees.ajax-search');
        Route::get('/employees/ajax-data', [EmployeeController::class, 'ajaxData'])
            ->name('employees.ajax-data');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    });

    // ========== Migration Verify (страница визуальной проверки миграции) ==========
    Route::prefix('migration')->group(function () {
        Route::get('/verify', [\App\Http\Controllers\Migration\MigrationVerifyController::class, 'index'])
            ->name('migration.verify');
        Route::get('/verify/{property}', [\App\Http\Controllers\Migration\MigrationVerifyController::class, 'verifyOne'])
            ->name('migration.verify-one');
        Route::post('/verify-batch', [\App\Http\Controllers\Migration\MigrationVerifyController::class, 'verifyBatch'])
            ->name('migration.verify-batch');
    });

    // ========== Settings ==========
    Route::prefix('settings')->name('settings.')->group(function () {
        // Settings pages (settings.view)
        Route::middleware('permission:settings.view')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::get('/users', [SettingsController::class, 'users'])->name('users.index');
            Route::get('/roles', [SettingsController::class, 'roles'])->name('roles.index');
            Route::get('/permissions', [SettingsController::class, 'permissions'])->name('permissions.index');

            // Location section pages
            Route::get('/countries', [SettingsController::class, 'countries'])->name('countries.index');
            Route::get('/regions', [SettingsController::class, 'regions'])->name('regions.index');
            Route::get('/oblast-regions', [SettingsController::class, 'oblastRegions'])->name('oblast-regions.index');
            Route::get('/cities', [SettingsController::class, 'cities'])->name('cities.index');
            Route::get('/districts', [SettingsController::class, 'districts'])->name('districts.index');
            Route::get('/zones', [SettingsController::class, 'zones'])->name('zones.index');
            Route::get('/streets', [SettingsController::class, 'streets'])->name('streets.index');
        });

        // Roles CRUD (settings.roles.manage)
        Route::middleware('permission:settings.roles.manage')->group(function () {
            Route::get('/roles/ajax-data', [RoleController::class, 'ajaxData'])->name('roles.ajax-data');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Users CRUD (settings.users.manage)
        Route::middleware('permission:settings.users.manage')->group(function () {
            Route::get('/users/ajax-data', [UserController::class, 'ajaxData'])->name('users.ajax-data');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Permissions Matrix (settings.permissions.manage)
        Route::middleware('permission:settings.permissions.manage')->group(function () {
            Route::get('/permissions/matrix', [PermissionController::class, 'matrix'])->name('permissions.matrix');
            Route::post('/permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
            Route::put('/permissions/role/{role}', [PermissionController::class, 'updateRole'])->name('permissions.update-role');
            Route::post('/permissions/bulk-update', [PermissionController::class, 'bulkUpdate'])->name('permissions.bulk-update');
        });

        // Location CRUD (settings.locations.manage)
        Route::middleware('permission:settings.locations.manage')->group(function () {
            // AJAX helpers
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
    });

});

require __DIR__.'/auth.php';
