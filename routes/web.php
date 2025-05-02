<?php

use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\TypeProductController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\TypePeriodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\TypeVatController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TypeContractController;
use App\Http\Controllers\Auth\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes(['register' => false]);

Route::group(['prefix' => '', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/tableau-de-bord', [HomeController::class, 'index'])->name('home');

    // Permissions
    Route::resource('permissions', PermissionController::class, ['except' => ['store', 'update', 'destroy']]);

    // Roles
    Route::resource('roles', RoleController::class, ['except' => ['store', 'update', 'destroy']]);

    // Owner
    Route::get('nos-coordonnees', [OwnerController::class, 'index'])->name('owners.index');
    Route::get('nos-coordonnees/creation', [OwnerController::class, 'create'])->name('owners.create');
    Route::get('nos-coordonnees/{owner}', [OwnerController::class, 'show'])->name('owners.show');
    Route::get('nos-coordonnees/{owner}/edit', [OwnerController::class, 'edit'])->name('owners.edit');

    // Users
    Route::get('utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('utilisateurs/creation', [UserController::class, 'create'])->name('users.create');
    Route::get('utilisateurs/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('utilisateurs/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Contract Type
    Route::get('type-de-contract', [TypeContractController::class, 'index'])->name('type-contract.index');
    Route::get('type-de-contract/creation', [TypeContractController::class, 'create'])->name('type-contract.create');
    Route::get('type-de-contract/{typeContract}', [TypeContractController::class, 'show'])->name('type-contract.show');
    Route::get('type-de-contract/{typeContract}/edit', [TypeContractController::class, 'edit'])->name('type-contract.edit');

    // Period Type
    Route::get('type-de-periode', [TypePeriodController::class, 'index'])->name('type-period.index');
    Route::get('type-de-periode/creation', [TypePeriodController::class, 'create'])->name('type-period.create');
    Route::get('type-de-periode/{typePeriod}', [TypePeriodController::class, 'show'])->name('type-period.show');
    Route::get('type-de-periode/{typePeriod}/edit', [TypePeriodController::class, 'edit'])->name('type-period.edit');

    // VAT Type
    Route::get('type-de-tva', [TypeVatController::class, 'index'])->name('type-vat.index');
    Route::get('type-de-tva/creation', [TypeVatController::class, 'create'])->name('type-vat.create');
    Route::get('type-de-tva/{typeVat}', [TypeVatController::class, 'show'])->name('type-vat.show');
    Route::get('type-de-tva/{typeVat}/edit', [TypeVatController::class, 'edit'])->name('type-vat.edit');

    // Product Type
    Route::get('type-de-produit', [TypeProductController::class, 'index'])->name('type-product.index');
    Route::get('type-de-produit/creation', [TypeProductController::class, 'create'])->name('type-product.create');
    Route::get('type-de-produit/{typeProduct}', [TypeProductController::class, 'show'])->name('type-product.show');
    Route::get('type-de-produit/{typeProduct}/edit', [TypeProductController::class, 'edit'])->name('type-product.edit');

    // Cities
    Route::get('villes', [CityController::class, 'index'])->name('cities.index');
    Route::get('villes/creation', [CityController::class, 'create'])->name('cities.create');
    Route::get('villes/{city}', [CityController::class, 'show'])->name('cities.show');
    Route::get('villes/{city}/edit', [CityController::class, 'edit'])->name('cities.edit');

    // Contacts
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/creation', [ContactController::class, 'create'])->name('contacts.create');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');

    // Bank Account
    Route::get('compte-bancaire', [BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::get('compte-bancaire/creation', [BankAccountController::class, 'create'])->name('bank-accounts.create');
    Route::get('compte-bancaire/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('compte-bancaire/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');

    // Companies
    Route::get('clients', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('clients/creation', [CompanyController::class, 'create'])->name('companies.create');
    Route::get('clients/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('clients/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');

    // Bills
    Route::get('factures', [BillController::class, 'index'])->name('bills.index');
    Route::get('factures/creation', [BillController::class, 'create'])->name('bills.create');
    Route::get('factures/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('factures/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::get('factures/{bill}/pdf', [BillController::class, 'pdf'])->name('bills.pdf');

    // Contracts
    Route::get('contrats', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contrats/creation/{company}', [ContractController::class, 'create'])->name('contracts.create');
    Route::get('contrats/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::get('contrats/previsualisation-html/{company}/{period}/{contracts}', [ContractController::class, 'previewHtml'])->withoutMiddleware('auth')->name('contracts.pdf.calculate.preview');
    Route::get('contrats/previsualisation/{company}/{period}/{contracts}', [ContractController::class, 'preview'])->name('contracts.pdf.preview');

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    })->name('notifications.markAllRead');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    if (file_exists(app_path('Http/Controllers/Auth/UserProfileController.php'))) {
        Route::get('/', [UserProfileController::class, 'show'])->name('show');
    }
});

Route::get('/install', [HomeController::class, 'install'])->name('install');

