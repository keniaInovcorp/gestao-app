<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ContactFunctionController;
use App\Http\Controllers\VatRateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CalendarTypeController;
use App\Http\Controllers\CalendarActionController;
use App\Http\Controllers\CalendarEventController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome');
});

Route::get('/company/logo', [CompanyController::class, 'logo'])->name('company.logo');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', '2fa.required'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('countries', CountryController::class);
    Route::resource('contact-functions', ContactFunctionController::class);
    Route::resource('calendar-types', CalendarTypeController::class);
    Route::resource('calendar-actions', CalendarActionController::class);
    Route::resource('calendar-events', CalendarEventController::class);
    Route::resource('vat-rates', VatRateController::class);
    Route::resource('products', ProductController::class);
    Route::get('/products/{product}/photo', [ProductController::class, 'photo'])->name('products.photo');
    Route::resource('contacts', ContactController::class);

    Route::get('/clients', function (Request $request) {
        return app(EntityController::class)->index($request->merge(['type' => 'client']));
    })->name('clients.index');
    
    Route::get('/clients/create', function (Request $request) {
        return app(EntityController::class)->create($request->merge(['type' => 'client']));
    })->name('clients.create');

    Route::get('/suppliers', function (Request $request) {
        return app(EntityController::class)->index($request->merge(['type' => 'supplier']));
    })->name('suppliers.index');
    
    Route::get('/suppliers/create', function (Request $request) {
        return app(EntityController::class)->create($request->merge(['type' => 'supplier']));
    })->name('suppliers.create');

    Route::post('/entities', [EntityController::class, 'store'])->name('entities.store');
    Route::get('/entities/{entity}', [EntityController::class, 'show'])->name('entities.show');
    Route::get('/entities/{entity}/edit', [EntityController::class, 'edit'])->name('entities.edit');
    Route::put('/entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');

    Route::post('/entities/validate-vies', [EntityController::class, 'validateVies'])->name('entities.validate-vies');

    Route::resource('quotations', QuotationController::class);
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::post('/quotations/{quotation}/convert-to-order', [QuotationController::class, 'convertToOrder'])->name('quotations.convert-to-order');

    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/pdf', [OrderController::class, 'pdf'])->name('orders.pdf');
    Route::post('/orders/{order}/convert-to-supplier-order', [OrderController::class, 'convertToSupplierOrder'])->name('orders.convert-to-supplier-order');

    Route::resource('supplier-orders', SupplierOrderController::class)->only(['index', 'show']);

    Route::resource('supplier-invoices', SupplierInvoiceController::class);
    Route::post('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'update'])->name('supplier-invoices.update.post');
    Route::get('/supplier-invoices/{supplierInvoice}/download-document', [SupplierInvoiceController::class, 'downloadDocument'])->name('supplier-invoices.download-document');
    Route::get('/supplier-invoices/{supplierInvoice}/download-payment-proof', [SupplierInvoiceController::class, 'downloadPaymentProof'])->name('supplier-invoices.download-payment-proof');

    Route::resource('users', UserController::class);
    Route::resource('permission-groups', PermissionGroupController::class);
    
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
    Route::post('/company', [CompanyController::class, 'update'])->name('company.update.post');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/calendar', [CalendarEventController::class, 'index'])->name('calendar.index');
});
