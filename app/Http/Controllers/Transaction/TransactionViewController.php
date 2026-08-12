<?php

namespace App\Http\Controllers\Transaction;

use App\DataTables\TransactionsDataTable;
use App\Feedbacks\TransactionFeedback;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

/**
 * This controller handles wallet view related requests
 */
class TransactionViewController extends Controller
{
    /**
     * Show all transactions for the user
     */
    public function list(TransactionsDataTable $dataTable): mixed
    {
        if (! Auth::user()->hasWallet()) {
            addSessionMsg(TransactionFeedback::noWalletMsg(), true);
        } elseif (! Auth::user()->hasAnyActiveWallet()) {
            addSessionMsg(TransactionFeedback::noActiveWalletMsg(), true);
        }

        return $dataTable->render('transaction.list');
    }

    /**
     * Show the view for creating a transaction
     */
    public function createItems(Request $request): View|Factory|RedirectResponse|Application
    {
        // pre-select the wallet, if there is intent
        // $wallet_id = $request->get('wallet_id');
        // if ($wallet_id !== null) {
        //     $wallet = Wallet::find($wallet_id);

        //     if ($wallet === null || $wallet->trashed() || !Auth::user()->owns($wallet)) {
        //         return WalletFeedback::quickCreateError('transaction');
        //     }
        // }

        if (! Auth::user()->hasAnyActiveWallet()) {
            return WalletFeedback::noWalletError(Auth::user()->hasWallet() ? 'active' : '');
        }

        $transaction = $this->loadPartialTransactionData($request);

        return view('transaction.create.items', compact('transaction'));
    }

    /**
     * Load the stored form data, if it is present, and we are coming from a create page.
     * If there is an error with the form, we load that data instead.
     *
     * @param  bool  $doUrlCheck  Check the source of the navigation and only allow transaction/create/* routes
     */
    private function loadPartialTransactionData(Request $request, bool $doUrlCheck = true): array
    {
        $prevURL = $request->session()->previousUrl();
        $hasErrors = $request->session()->has('errors');
        $data = $hasErrors ? old() : [];

        if ($data === [] || ((count($data) === 1) && isset($data['_token']))) {
            if (! $doUrlCheck || Str::is(url()->to('/').'/transactions/create/*', $prevURL)) {
                $data = $hasErrors ? old() : $request->session()->get('transaction') ?? [];
            } else {
                $request->session()->forget('transaction');
            }
        } else {
            unset($data['_token']);
        }

        return $data;
    }

    /**
     * Payment view for the transaction
     */
    public function createPayment(Request $request): Factory|View|Application
    {
        $transaction = $this->loadPartialTransactionData($request, false);

        return view('transaction.create.payment', compact('transaction'));
    }

    /**
     * Overview view for the transaction
     */
    public function createOverview(Request $request): Factory|View|Application
    {
        $transaction = $this->loadPartialTransactionData($request);

        return view('transaction.create.overview', compact('transaction'));
    }

    /**
     * Show the view for editing a movie
     *
     * see also: https://stackoverflow.com/a/59745972
     */
    public function edit(string $id): View|Factory|RedirectResponse|Application
    {
        if (! Auth::user()->hasWallet()) {
            return WalletFeedback::noWalletError();
        }

        $transaction = Transaction::find($id);

        $permissionCheck = Transaction::checkStatus($transaction);

        return $permissionCheck ?? view('transaction.edit', compact('transaction'));
    }
}
