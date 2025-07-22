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


    Route::get('articles', [ContractController::class, 'impacted2025'])->name('contracts.impacted2025');

    Route::get('/tableau-de-bord', [HomeController::class, 'index'])->name('home');

    Route::resource('permissions', PermissionController::class, ['except' => ['store', 'update', 'destroy']]);

    Route::resource('roles', RoleController::class, ['except' => ['store', 'update', 'destroy']]);

    Route::get('nos-coordonnees', [OwnerController::class, 'index'])->name('owners.index');
    Route::get('nos-coordonnees/creation', [OwnerController::class, 'create'])->name('owners.create');
    Route::get('nos-coordonnees/{owner}', [OwnerController::class, 'show'])->name('owners.show');
    Route::get('nos-coordonnees/{owner}/edition', [OwnerController::class, 'edit'])->name('owners.edit');

    Route::get('utilisateurs', [UserController::class, 'index'])->name('users.index');
    Route::get('utilisateurs/creation', [UserController::class, 'create'])->name('users.create');
    Route::get('utilisateurs/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('utilisateurs/{user}/edition', [UserController::class, 'edit'])->name('users.edit');

    Route::get('type-de-contract', [TypeContractController::class, 'index'])->name('type-contract.index');
    Route::get('type-de-contract/creation', [TypeContractController::class, 'create'])->name('type-contract.create');
    Route::get('type-de-contract/{typeContract}', [TypeContractController::class, 'show'])->name('type-contract.show');
    Route::get('type-de-contract/{typeContract}/edition', [TypeContractController::class, 'edit'])->name('type-contract.edit');

    Route::get('type-de-periode', [TypePeriodController::class, 'index'])->name('type-period.index');
    Route::get('type-de-periode/creation', [TypePeriodController::class, 'create'])->name('type-period.create');
    Route::get('type-de-periode/{typePeriod}', [TypePeriodController::class, 'show'])->name('type-period.show');
    Route::get('type-de-periode/{typePeriod}/edition', [TypePeriodController::class, 'edit'])->name('type-period.edit');

    Route::get('type-de-tva', [TypeVatController::class, 'index'])->name('type-vat.index');
    Route::get('type-de-tva/creation', [TypeVatController::class, 'create'])->name('type-vat.create');
    Route::get('type-de-tva/{typeVat}', [TypeVatController::class, 'show'])->name('type-vat.show');
    Route::get('type-de-tva/{typeVat}/edition', [TypeVatController::class, 'edit'])->name('type-vat.edit');

    Route::get('type-de-produit', [TypeProductController::class, 'index'])->name('type-product.index');
    Route::get('type-de-produit/creation', [TypeProductController::class, 'create'])->name('type-product.create');
    Route::get('type-de-produit/{typeProduct}', [TypeProductController::class, 'show'])->name('type-product.show');
    Route::get('type-de-produit/{typeProduct}/edition', [TypeProductController::class, 'edit'])->name('type-product.edit');

    Route::get('villes', [CityController::class, 'index'])->name('cities.index');
    Route::get('villes/creation', [CityController::class, 'create'])->name('cities.create');
    Route::get('villes/{city}', [CityController::class, 'show'])->name('cities.show');
    Route::get('villes/{city}/edition', [CityController::class, 'edit'])->name('cities.edit');

    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/creation', [ContactController::class, 'create'])->name('contacts.create');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('contacts/{contact}/edition', [ContactController::class, 'edit'])->name('contacts.edit');

    Route::get('compte-bancaire', [BankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::get('compte-bancaire/creation', [BankAccountController::class, 'create'])->name('bank-accounts.create');
    Route::get('compte-bancaire/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('compte-bancaire/{bankAccount}/edition', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');

    Route::get('clients', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('clients/creation', [CompanyController::class, 'create'])->name('companies.create');
    Route::get('clients/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('clients/{company}/edition', [CompanyController::class, 'edit'])->name('companies.edit');

    Route::get('factures', [BillController::class, 'index'])->name('bills.index');
    Route::get('factures/creation', [BillController::class, 'create'])->name('bills.create');
    Route::get('factures/export-prelevement/{dateStart}/{dateEnd}', [BillController::class, 'export_order_prlv'])->where([
        'dateStart' => '\d{4}-\d{2}-\d{2}',
        'dateEnd'   => '\d{4}-\d{2}-\d{2}',
    ])->name('bills.export_order_prlv');
    Route::get('factures/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('factures/{bill}/edition', [BillController::class, 'edit'])->name('bills.edit');
    Route::get('factures/{bill}/pdf', [BillController::class, 'pdf'])->name('bills.pdf');
    Route::get('factures/{bill}/pdf/previsualisation', [BillController::class, 'pdfStream'])->name('bills.pdf.stream');

    Route::get('contrats', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contrats/annuels', [ContractController::class, 'annuals'])->name('contracts.annual-index');
    Route::get('contrats/creation/{company}', [ContractController::class, 'create'])->name('contracts.create');
    Route::get('contrats/{contract}/edition', [ContractController::class, 'edit'])->name('contracts.edit');
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

