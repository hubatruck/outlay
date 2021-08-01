<?php

use App\DataTables\TransactionsDataTable;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Wallet\WalletEditController;
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
    'confirm' => false
]);

Route::get('/', function () {
    return redirect('/home');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::prefix('wallets')->group(function () {
        Route::get('/', function () {
            return view('wallet/list');
        })->name('wallet.view.all');

        Route::get('create', [WalletEditController::class, 'createView'])->name('wallet.view.create');
        Route::post('create', [WalletEditController::class, 'storeWallet'])->name('wallet.data.create');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', [WalletEditController::class, 'editView'])->name('wallet.view.update');
            Route::post('edit', [WalletEditController::class, 'updateWallet'])->name('wallet.data.update');
        });
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', function (TransactionsDataTable $dataTable) {
            return $dataTable->render('transaction/list');
        })->name('transaction.view.all');

        Route::get('create', [TransactionController::class, 'createView'])->name('transaction.view.create');
        Route::post('create', [TransactionController::class, 'storeTransaction'])->name('transaction.data.create');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', [TransactionController::class, 'editView'])->name('transaction.view.update');
            Route::post('edit', [TransactionController::class, 'updateTransaction'])->name('transaction.data.update');
            Route::get('delete', [TransactionController::class, 'deleteTransaction'])->name('transaction.data.delete');
        });
    });
});
