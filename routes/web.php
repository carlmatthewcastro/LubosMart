<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [ShopController::class, 'index'])->name('home');

// ===== Marketplace: Buyer & Seller (unified) =====
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'marketplaceRegister'])->name('register.submit');

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'marketplaceLogin'])->name('login.submit');

// Forgot / reset password (buyer & seller). The route name 'password.reset' is
// required: Laravel's reset email builds its link from it.
Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])
    ->middleware('throttle:6,1')
    ->name('password.update');

Route::post('/logout', [AuthController::class, 'marketplaceLogout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

Route::post('/cart/add', [CartController::class, 'store'])
    ->middleware('auth')
    ->name('cart.add');

Route::get('/shop/product/{product}', [ShopController::class, 'show'])->name('shop.product');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

Route::get('/seller/dashboard', fn () => view('seller.dashboard.stub'))
    ->middleware('auth')
    ->name('seller.dashboard');

// ===== Super Admin =====
Route::get('/superadmin/login', function () {
    return view('superadmin.login');
})->name('superadmin.login');

Route::post('/superadmin/login', [AuthController::class, 'login'])
    ->defaults('role', 'superadmin')
    ->name('superadmin.login.submit');

Route::get('/superadmin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('auth')
    ->name('superadmin.dashboard');

Route::post('/superadmin/approve/{user}', [AdminController::class, 'approve'])
    ->middleware('auth')
    ->name('superadmin.approve');

Route::post('/superadmin/reject/{user}', [AdminController::class, 'reject'])
    ->middleware('auth')
    ->name('superadmin.reject');

Route::post('/superadmin/logout', [AuthController::class, 'logout'])
    ->defaults('role', 'superadmin')
    ->name('superadmin.logout');

Route::get('/superadmin/buyers', function () {
    return view('superadmin.buyers');
})->middleware('auth')->name('superadmin.buyers');

Route::get('/superadmin/sellers', function () {
    return view('superadmin.sellers');
})->middleware('auth')->name('superadmin.sellers');

Route::get('/superadmin/riders', function () {
    $all = \App\Models\User::where('role', 'rider')->latest()->get();
    return view('superadmin.riders', compact('all'));
})->middleware('auth')->name('superadmin.riders');

Route::get('/superadmin/companies', function () {
    $all = \App\Models\User::where('role', 'logistics')->latest()->get();
    return view('superadmin.companies', compact('all'));
})->middleware('auth')->name('superadmin.companies');

Route::get('/superadmin/categories', function () {
    return view('superadmin.categories');
})->middleware('auth')->name('superadmin.categories');

Route::get('/superadmin/reports', function () {
    return view('superadmin.reports');
})->middleware('auth')->name('superadmin.reports');

Route::get('/superadmin/settings', function () {
    return view('superadmin.settings');
})->middleware('auth')->name('superadmin.settings');

// ===== Rider =====
Route::get('/rider/register', function () {
    return view('rider.register');
})->name('rider.register');

Route::post('/rider/register', [AuthController::class, 'register'])
    ->defaults('role', 'rider')
    ->name('rider.register.submit');

Route::get('/rider/login', function () {
    return view('rider.login');
})->name('rider.login');

Route::post('/rider/login', [AuthController::class, 'login'])
    ->defaults('role', 'rider')
    ->name('rider.login.submit');

Route::get('/rider/pending-approval', function () {
    return view('rider.pending-approval');
})->middleware('auth')->name('rider.pending');

Route::get('/rider/choose-company', function () {
    return view('rider.choose-company');
})->middleware('auth')->name('rider.choose-company');

Route::post('/rider/apply-company', [RiderController::class, 'applyToCompany'])
    ->middleware('auth')
    ->name('rider.apply-company');

Route::get('/rider/dashboard', function () {
    return view('rider.dashboard');
})->middleware('auth')->name('rider.dashboard');

Route::post('/rider/logout', [AuthController::class, 'logout'])
    ->defaults('role', 'rider')
    ->name('rider.logout');

// ===== Logistics Company =====
Route::get('/logistics/register', function () {
    return view('logistics.register');
})->name('logistics.register');

Route::post('/logistics/register', [AuthController::class, 'register'])
    ->defaults('role', 'logistics')
    ->name('logistics.register.submit');

Route::get('/logistics/login', function () {
    return view('logistics.login');
})->name('logistics.login');

Route::post('/logistics/login', [AuthController::class, 'login'])
    ->defaults('role', 'logistics')
    ->name('logistics.login.submit');

Route::get('/logistics/dashboard', function () {
    return view('logistics.dashboard');
})->middleware('auth')->name('logistics.dashboard');

Route::post('/logistics/logout', [AuthController::class, 'logout'])
    ->defaults('role', 'logistics')
    ->name('logistics.logout');