<?php

use App\Charts\MonthlyChartByDay;
use App\Charts\MonthlyChartByTransactionType;
use App\DataTables\TransactionsDataTable;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Wallet\WalletController;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LarapexChart;
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

            Route::get('details', function ($id) {
                $dailyChart = (new MonthlyChartByDay(new LarapexChart()))->build($id);
                $typeChart = (new MonthlyChartByTransactionType(new LarapexChart()))->build($id);
                $wallet = Wallet::withTrashed()->findOrFail($id);
                return view('wallet.details', compact('dailyChart', 'typeChart', 'wallet'));
            })->name('wallet.view.details');

            Route::get('delete', [WalletController::class, 'deleteWallet'])->name('wallet.manage.delete');
            Route::get('toggle_hidden', [WalletController::class, 'toggleHidden'])->name('wallet.manage.toggle_hidden');
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
