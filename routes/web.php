<?php

use App\Http\Controllers\Access\ForgoutController;
use App\Http\Controllers\Access\LoginController;
use App\Http\Controllers\Access\RegisterController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Contract\ContractController;
use App\Http\Controllers\Contract\TemplateController;
use App\Http\Controllers\Finance\WalletController;
use App\Http\Controllers\Finance\WithdrawalController;
use App\Http\Controllers\Gateway\CoraController;
use App\Http\Controllers\Product\PaymentOptionController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\SubscriptionController;
use App\Http\Controllers\Sale\ImportController;
use App\Http\Controllers\Sale\ListController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/logon', [LoginController::class, 'store'])->name('logon');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/registrer/{parent?}', [RegisterController::class, 'store'])->name('registrer');

Route::get('/forgout/{code?}', [ForgoutController::class, 'index'])->name('forgout');
Route::post('/forgout-password', [ForgoutController::class, 'store'])->name('forgout-password');
Route::post('/recover-password/{code}', [ForgoutController::class, 'update'])->name('recover-password');

Route::get('/contract/{uuid}', [ContractController::class, 'show'])->name('contract');
Route::post('/updated-contract/{uuid}', [ContractController::class, 'update'])->name('updated-contract');

Route::middleware(['auth'])->group(function () {

    Route::get('/app', [AppController::class, 'index'])->name('app');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('/created-subscription/{uuid}', [SubscriptionController::class, 'store'])->name('created-subscription');

    Route::get('/user/{uuid}', [UserController::class, 'show'])->name('user');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/updated-user/{uuid}', [UserController::class, 'update'])->name('updated-user');
    Route::post('/created-user', [UserController::class, 'store'])->name('created-user');
    Route::post('/deleted-user/{uuid}', [UserController::class, 'destroy'])->name('deleted-user');

    Route::get('/deploy', [CoraController::class, 'getToken'])->name('deploy');

    Route::middleware(['CheckIsMonthly'])->group(function () {

        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
        Route::post('/created-deposit', [WalletController::class, 'store'])->name('created-deposit');

        Route::post('/created-withdrawal', [WithdrawalController::class, 'store'])->name('created-withdrawal');
        Route::post('/deleted-withdrawal/{uuid}', [WithdrawalController::class, 'destroy'])->name('deleted-withdrawal');

        Route::get('/sales', [SaleController::class, 'index'])->name('sales');
        Route::post('/created-sale', [SaleController::class, 'store'])->name('created-sale');
        Route::post('/updated-sale/{uuid}', [SaleController::class, 'update'])->name('updated-sale');
        Route::post('/deleted-sale/{uuid}', [SaleController::class, 'destroy'])->name('deleted-sale');
        Route::post('/created-import-sale', [ImportController::class, 'store'])->name('created-import-sale');

        Route::get('/lists/{type?}', [ListController::class, 'index'])->name('lists');
        Route::post('/export-list/{uuid}', [ListController::class, 'export'])->name('export-list');

        Route::get('/products/{type?}', [ProductController::class, 'index'])->name('products');
        Route::get('/product/{uuid}', [ProductController::class, 'show'])->name('product');

        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts');
        Route::post('/created-contract', [ContractController::class, 'store'])->name('created-contract');
        Route::post('/deleted-contract/{uuid}', [ContractController::class, 'destroy'])->name('deleted-contract');
    });

    Route::middleware(['CheckIsAdmin'])->group(function () {

        Route::post('/created-product', [ProductController::class, 'store'])->name('created-product');
        Route::post('/updated-product/{uuid}', [ProductController::class, 'update'])->name('updated-product');
        Route::post('/deleted-product/{uuid}', [ProductController::class, 'destroy'])->name('deleted-product');

        Route::post('/created-payment-option/{product}', [PaymentOptionController::class, 'store'])->name('created-payment-option');
        Route::post('/deleted-payment-option/{uuid}', [PaymentOptionController::class, 'destroy'])->name('deleted-payment-option');

        Route::post('/created-list', [ListController::class, 'store'])->name('created-list');
        Route::post('/updated-list/{uuid}', [ListController::class, 'update'])->name('updated-list');
        Route::post('/deleted-list/{uuid}', [ListController::class, 'destroy'])->name('deleted-list');

        Route::get('/templates', [TemplateController::class, 'index'])->name('templates');
        Route::get('/template/{uuid}', [TemplateController::class, 'show'])->name('template');
        Route::post('/created-template', [TemplateController::class, 'store'])->name('created-template');
        Route::post('/updated-template/{uuid}', [TemplateController::class, 'update'])->name('updated-template');
        Route::post('/deleted-template/{uuid}', [TemplateController::class, 'destroy'])->name('deleted-template');

        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals');
        Route::post('/send-withdrawal', [WithdrawalController::class, 'sendWithdrawal'])->name('send-withdrawal');  
    });

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});