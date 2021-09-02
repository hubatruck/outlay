<?php

namespace App\Http\Controllers\Transaction;

use App\DataTables\TransactionsDataTable;
use App\Feedbacks\TransactionFeedback;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletIsActiveRule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    private string $editViewName = 'transaction/edit';

    /**
     * Show all transactions for the user
     *
     * @param TransactionsDataTable $dataTable
     * @return mixed
     */
    public function index(TransactionsDataTable $dataTable): mixed
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
    public function createView(Request $request): View|Factory|RedirectResponse|Application
    {
        /// pre-select the wallet, if there is intent
        $wallet_id = $request->get('wallet_id');
        if ($wallet_id !== null) {
            $wallet = Wallet::find($wallet_id);

            if ($wallet === null || $wallet->trashed() || !Auth::user()->owns($wallet)) {
                return WalletFeedback::quickCreateError();
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
    public function editView(string $id): View|Factory|RedirectResponse|Application
    {
        if (!Auth::user()->hasWallet()) {
            return WalletFeedback::noWalletError();
        }

        $transaction = Transaction::find($id);

        $permissionCheck = Transaction::checkStatus($transaction);

        return $permissionCheck ?? view($this->editViewName, compact('transaction'));
    }

    /**
     * Store a transaction in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeTransaction(Request $request): RedirectResponse
    {
        $newTransactionData = $this->validateRequest($request);
        Transaction::create($newTransactionData);

        return TransactionFeedback::success();
    }

    /**
     * Validate request data
     *
     * @param Request $request
     * @param bool $walletMustBeActive
     * @return array
     */
    public function validateRequest(Request $request, bool $walletMustBeActive = true): array
    {
        $walletRules = ['bail'];
        if ($walletMustBeActive) {
            $walletRules[] = new UserOwnsWalletRule();
            $walletRules[] = new WalletIsActiveRule();
            $walletRules[] = Auth::user()->hasAnyActiveWallet() ? 'required' : 'nullable';
        }

        return $request->validate([
            'wallet_id' => $walletRules,
            'scope' => 'required|max:255',
            'amount' => 'numeric|min:0.01|max:999999.99',
            'transaction_type_id' => [
                'required',
                Rule::in(TransactionType::all()->pluck('id')->toArray()),
            ],
            'transaction_date' => 'required|date|date_format:' . globalDateFormat(),
        ]);
    }

    /**
     * Update a transaction
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateTransaction(Request $request, string $id): RedirectResponse
    {
        $oldTransaction = Transaction::find($id);
        $permissionCheck = Transaction::checkStatus($oldTransaction);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $wallet_id = $request->get('wallet_id');
        $walletMustBeActive = $wallet_id && ((string) $oldTransaction->wallet_id !== (string) $wallet_id);
        $validated = $this->validateRequest($request, $walletMustBeActive);

        $updatedTransaction = new Transaction($validated);
        if ($updatedTransaction->wallet && !Auth::user()->owns($updatedTransaction)) {
            return TransactionFeedback::editError();
        }

        $oldTransaction->fill($updatedTransaction->attributesToArray());
        $oldTransaction->save();
        return TransactionFeedback::success('updated');
    }

    /**
     * Delete a transaction from the database
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function deleteTransaction(string $id): RedirectResponse
    {
        $transaction = Transaction::find($id);

        $permissionCheck = Transaction::checkStatus($transaction);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $transaction->delete();
        return TransactionFeedback::success('removed');
    }
}
