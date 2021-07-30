<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'confirm' => false
]);

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('wallets')->middleware(['auth'])->group(function () {
    Route::get('/create', [WalletEditController::class, 'createView'])->name('wallet.view.create');
    Route::get('/edit/{id}', [WalletEditController::class, 'editView'])->name('wallet.view.update');
    Route::post('/create', [WalletEditController::class, 'storeWallet'])->name('wallet.data.create');
    Route::post('/edit/{id}', [WalletEditController::class, 'updateWallet'])->name('wallet.data.update');
});
