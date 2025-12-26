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

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(Features::enabled(Features::twoFactorAuthentication()) ? '2fa' : null);

    Route::resource('countries', CountryController::class);
    Route::resource('contact-functions', ContactFunctionController::class);
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
});
