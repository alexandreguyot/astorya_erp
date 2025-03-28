<?php

use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PeriodTypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VatTypeController;
use App\Http\Controllers\Auth\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Permissions
    Route::resource('permissions', PermissionController::class, ['except' => ['store', 'update', 'destroy']]);

    // Roles
    Route::resource('roles', RoleController::class, ['except' => ['store', 'update', 'destroy']]);

    // Users
    Route::resource('users', UserController::class, ['except' => ['store', 'update', 'destroy']]);

    // Contract Type
    Route::resource('contract-types', ContractTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Period Type
    Route::resource('period-types', PeriodTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Vat Type
    Route::resource('vat-types', VatTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Product Type
    Route::resource('product-types', ProductTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Owner
    Route::resource('owners', OwnerController::class, ['except' => ['store', 'update', 'destroy']]);

    // City
    Route::resource('cities', CityController::class, ['except' => ['store', 'update', 'destroy']]);

    // Contact
    Route::resource('contacts', ContactController::class, ['except' => ['store', 'update', 'destroy']]);

    // Bank Account
    Route::resource('bank-accounts', BankAccountController::class, ['except' => ['store', 'update', 'destroy']]);

    // Companies
    Route::resource('companies', CompanyController::class, ['except' => ['store', 'update', 'destroy']]);

    // Bill
    Route::post('bills/media', [BillController::class, 'storeMedia'])->name('bills.storeMedia');
    Route::resource('bills', BillController::class, ['except' => ['store', 'update', 'destroy']]);

    // Contract
    Route::resource('contracts', ContractController::class, ['except' => ['store', 'update', 'destroy']]);
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    if (file_exists(app_path('Http/Controllers/Auth/UserProfileController.php'))) {
        Route::get('/', [UserProfileController::class, 'show'])->name('show');
    }
});

Route::get('/install', [HomeController::class, 'install'])->name('install');
