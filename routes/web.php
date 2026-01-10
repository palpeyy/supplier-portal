<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodReceiptController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return view('home');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/vendor', [VendorController::class, 'index']);
    Route::get('/material', [MaterialController::class, 'index']);

    Route::get('/purchase-request', [PurchaseRequestController::class, 'index']);
    Route::get('/purchase-order', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-order-approval', [PurchaseOrderController::class, 'approval']);

    Route::get('/surat-jalan', [SuratJalanController::class, 'index']);
    Route::get('/surat-jalan-approval', [SuratJalanController::class, 'approval']);

    Route::get('/good-receipt', [GoodReceiptController::class, 'index']);
    Route::get('/good-receipt-history', [GoodReceiptController::class, 'history']);

    Route::get('/purchase-invoice', [PurchaseInvoiceController::class, 'index']);
    Route::get('/purchase-invoice-history', [PurchaseInvoiceController::class, 'pembayaran']);

    // Manajemen User - CRUD Routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});
