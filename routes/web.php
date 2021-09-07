<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Transaction\TransactionDataController;
use App\Http\Controllers\Transaction\TransactionViewController;
use App\Http\Controllers\Transfer\TransferDataController;
use App\Http\Controllers\Transfer\TransferViewController;
use App\Http\Controllers\Wallet\ChartController;
use App\Http\Controllers\Wallet\WalletDataController;
use App\Http\Controllers\Wallet\WalletViewController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false,
]);

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('wallets')->group(function () {
        Route::get('/', [WalletViewController::class, 'listView'])->name('wallet.view.all');

        Route::get('create', [WalletViewController::class, 'createView'])->name('wallet.view.create');
        Route::post('create', [WalletDataController::class, 'storeWallet'])->name('wallet.data.create');

        Route::prefix('{id}')->group(function () {
            Route::get('edit', [WalletViewController::class, 'editView'])->name('wallet.view.update');
            Route::post('edit', [WalletDataController::class, 'updateWallet'])->name('wallet.data.update');

            Route::get('details', [WalletViewController::class, 'detailsView'])->name('wallet.view.details');
            Route::get('charts', [ChartController::class, 'getFor'])->name('wallet.view.charts');

            Route::get('delete', [WalletDataController::class, 'deleteWallet'])->name('wallet.manage.delete');
            Route::get('toggle_hidden', [WalletDataController::class, 'toggleHidden'])->name('wallet.manage.toggle_hidden');
        });
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionViewController::class, 'listView'])->name('transaction.view.all');

        Route::get('create', [TransactionViewController::class, 'createView'])->name('transaction.view.create');
        Route::post('create', [TransactionDataController::class, 'storeTransaction'])->name('transaction.data.create');

        Route::prefix('{id}')->group(function () {
            Route::get('edit', [TransactionViewController::class, 'editView'])->name('transaction.view.update');
            Route::post('edit', [TransactionDataController::class, 'updateTransaction'])->name('transaction.data.update');
            Route::get('delete', [TransactionDataController::class, 'deleteTransaction'])->name('transaction.data.delete');
        });
    });

    Route::prefix('transfers')->group(function () {
        Route::get('/', [TransferViewController::class, 'listView'])->name('transfer.view.all');

        Route::get('create', [TransferViewController::class, 'createView'])->name('transfer.view.create');
        Route::post('create', [TransferDataController::class, 'storeTransfer'])->name('transfer.data.create');
    });
});

Route::view('dashboard', 'dashboard')
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::prefix('user')->middleware(['auth', 'verified'])->group(function () {
    Route::view('profile', 'profile.show');
});
