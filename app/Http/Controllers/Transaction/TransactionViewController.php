<?php

namespace App\Http\Controllers\Transaction;

use App\DataTables\TransactionsDataTable;
use App\Feedbacks\TransactionFeedback;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This controller handles wallet view related requests
 */
class TransactionViewController extends Controller
{
    private string $editViewName = 'transaction/edit';

    /**
     * Show all transactions for the user
     *
     * @param TransactionsDataTable $dataTable
     * @return mixed
     */
    public function list(TransactionsDataTable $dataTable): mixed
    {
        if (!Auth::user()->hasWallet()) {
            addSessionMsg(TransactionFeedback::noWalletMsg(), true);
        } else if (!Auth::user()->hasAnyActiveWallet()) {
            addSessionMsg(TransactionFeedback::noActiveWalletMsg(), true);
        }
        return $dataTable->render('transaction.list');
    }

    /**
     * Show the view for creating a transaction
     *
     * @param Request $request
     * @return View|Factory|RedirectResponse|Application
     */
    public function create(Request $request): View|Factory|RedirectResponse|Application
    {
        /// pre-select the wallet, if there is intent
        $wallet_id = $request->get('wallet_id');
        if ($wallet_id !== null) {
            $wallet = Wallet::find($wallet_id);

            if ($wallet === null || $wallet->trashed() || !Auth::user()->owns($wallet)) {
                return WalletFeedback::quickCreateError('transaction');
            }
        }

        if (!Auth::user()->hasAnyActiveWallet()) {
            return WalletFeedback::noWalletError(Auth::user()->hasWallet() ? 'active' : '');
        }
        return view($this->editViewName, ['selected_wallet_id' => $request->wallet_id ?? '-1']);
    }

    /**
     * Show the view for editing a movie
     *
     * see also: https://stackoverflow.com/a/59745972
     *
     * @param string $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit(string $id): View|Factory|RedirectResponse|Application
    {
        if (!Auth::user()->hasWallet()) {
            return WalletFeedback::noWalletError();
        }

        $transaction = Transaction::find($id);

        $permissionCheck = Transaction::checkStatus($transaction);

        return $permissionCheck ?? view($this->editViewName, compact('transaction'));
    }
}
