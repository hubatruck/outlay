<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transfer\TransferController;
use App\Http\Controllers\Wallet\WalletController;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Wallet;
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
    return redirect('/home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    if (config('app.debug')) {
        Route::get('debug', function () {
            return view('debug');
        })->name('debug');
    }

    Route::prefix('wallets')->group(function () {
        Route::get('/', function () {
            $wallets = Auth::user()->wallets()->get()->sortBy('deleted_at');
            return view('wallet/list', compact('wallets'));
        })->name('wallet.view.all');

        Route::get('create', [WalletController::class, 'createView'])->name('wallet.view.create');
        Route::post('create', [WalletController::class, 'storeWallet'])->name('wallet.data.create');

        Route::prefix('{id}')->group(function () {
            Route::get('edit', [WalletController::class, 'editView'])->name('wallet.view.update');
            Route::post('edit', [WalletController::class, 'updateWallet'])->name('wallet.data.update');

            Route::get('details', [WalletController::class, 'detailsView'])->name('wallet.view.details');

            Route::get('delete', [WalletController::class, 'deleteWallet'])->name('wallet.manage.delete');
            Route::get('toggle_hidden', [WalletController::class, 'toggleHidden'])->name('wallet.manage.toggle_hidden');

            if (config('app.debug')) {
                Route::get('debug', function ($id) {
                    $wallet = Wallet::findOrFail($id);
                    return view('wallet.debug', ['wallet' => $wallet]);
                })->name('wallet.view.debug');
            }
        });
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transaction.view.all');

        Route::get('create', [TransactionController::class, 'createView'])->name('transaction.view.create');
        Route::post('create', [TransactionController::class, 'storeTransaction'])->name('transaction.data.create');

        Route::prefix('{id}')->group(function () {
            Route::get('edit', [TransactionController::class, 'editView'])->name('transaction.view.update');
            Route::post('edit', [TransactionController::class, 'updateTransaction'])->name('transaction.data.update');
            Route::get('delete', [TransactionController::class, 'deleteTransaction'])->name('transaction.data.delete');

            if (config('app.debug')) {
                Route::get('debug', function ($id) {
                    $transaction = Transaction::findOrFail($id);
                    return view('transaction.debug', ['transaction' => $transaction]);
                })->name('transaction.view.debug');
            }
        });
    });

    Route::prefix('transfers')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('transfer.view.all');

        Route::get('create', [TransferController::class, 'createView'])->name('transfer.view.create');
        Route::post('create', [TransferController::class, 'storeTransfer'])->name('transfer.data.create');

        Route::prefix('{id}')->group(function () {
            Route::get('debug', function ($id) {
                $transfer = Transfer::findOrFail($id);
                return view('transfer.debug', ['transfer' => $transfer]);
            })->name('transfer.view.debug');
        });
    });
});

Route::view('dashboard', 'dashboard')
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::prefix('user')->middleware(['auth', 'verified'])->group(function () {
    Route::view('profile', 'profile.show');
});
