<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletAvailable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    private $viewName = 'transaction/edit';

    /**
     * Show the view for creating a transaction
     *
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function createView(Request $request)
    {
        /// pre-select the wallet, if there is intent
        if ($request->wallet_id) {
            $wallet = Wallet::find($request->wallet_id);

            if ($wallet === null || !Auth::user()->owns($wallet) || $wallet->trashed()) {
                return $this->redirect(route('wallet.view.all'), [
                    'status' => 'danger',
                    'message' => __('Error: ') . __('Wallet unavailable for quick transaction creation.'),
                ]);
            }
        }

        if (!Auth::user()->hasAnyActiveWallet()) {
            return $this->noWallet(Auth::user()->hasWallet() ? 'active' : '');
        }
        return view($this->viewName, ['selected_wallet_id' => $request->wallet_id ?? '-1']);
    }

    /**
     * Redirect user with 'no wallet found' error
     *
     * @param string $type
     * @return RedirectResponse
     */
    public function noWallet(string $type = ''): RedirectResponse
    {
        return $this->redirect(previousUrlOr(route('transaction.view.all')), [
            'message' => __('Error: ') . __(
                    'No :type wallet linked to your account found.', [
                        'type' => __($type),
                    ]
                ),
            'status' => 'danger',
        ]);
    }

    /**
     * Show the view for editing a movie
     *
     * see also: https://stackoverflow.com/a/59745972
     *
     * @param string $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function editView(string $id)
    {
        if (!count(Auth::user()->wallets ?? null)) {
            return $this->noWallet();
        }

        $transaction = Transaction::find($id);
        if (empty($transaction) || $transaction->wallet === null) {
            return $this->transactionDoesNotExist();
        }
        if (!Auth::user()->owns($transaction)) {
            return $this->cannotEditTransaction();
        }
        return view($this->viewName, compact('transaction'));
    }

    /**
     * Redirect user with 'transaction does not exist' error
     *
     * @return RedirectResponse
     */
    private function transactionDoesNotExist(): RedirectResponse
    {
        return $this->redirect(route('transaction.view.all'),
            [
                'message' => __('Error: ') . __('Transaction does not exist.'),
                'status' => 'danger',
            ]);
    }

    /**
     * Redirect user with 'cannot edit this transaction' error
     *
     * @return RedirectResponse
     */
    private function cannotEditTransaction(): RedirectResponse
    {
        return $this->redirect(route('transaction.view.all'),
            [
                'message' => __('Error: ') . __('You cannot edit this transaction.'),
                'status' => 'danger',
            ]);
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

        return $this->redirectSuccess();
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
            $walletRules[] = new WalletAvailable();
            $walletRules[] = Auth::user()->hasAnyActiveWallet() ? 'required' : 'nullable';
        }

        return $request->validate([
            'wallet_id' => $walletRules,
            'scope' => 'required|max:255',
            'amount' => 'numeric|max:999999.99',
            'transaction_type_id' => [
                'required',
                Rule::in(TransactionType::all()->pluck('id')->toArray())
            ],
            'transaction_date' => 'required|date|date_format:Y-m-d',
        ]);
    }

    /**
     * Redirect user with success message
     *
     * @param string $successMethod
     * @return RedirectResponse
     */
    private function redirectSuccess(string $successMethod = 'created'): RedirectResponse
    {
        return $this->redirect(route('transaction.view.all'),
            [
                'message' => __(
                    'Transaction :action successfully.', [
                        'action' => __($successMethod)
                    ]
                ),
                'status' => 'success',
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
        if ($oldTransaction === null) {
            $this->transactionDoesNotExist();
        }

        $walletMustBeActive = $request->wallet_id && ((string) $oldTransaction->wallet_id !== (string) $request->wallet_id);
        $validated = $this->validateRequest($request, $walletMustBeActive);

        $updatedTransaction = new Transaction($validated);
        if (
            !Auth::user()->owns($oldTransaction)
            || ($updatedTransaction->wallet && !Auth::user()->owns($updatedTransaction))
        ) {
            return $this->cannotEditTransaction();
        }

        $oldTransaction->fill($updatedTransaction->attributesToArray());
        $oldTransaction->save();
        return $this->redirectSuccess('updated');
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

        if (empty($transaction) || $transaction->wallet === null) {
            return $this->transactionDoesNotExist();
        }
        if (!Auth::user()->owns($transaction)) {
            return $this->cannotEditTransaction();
        }

        $transaction->delete();
        return $this->redirectSuccess('removed');
    }

    /**
     * Redirect wrapper function
     *
     * @param string $url
     * @param array|null $response
     * @return RedirectResponse
     */
    private function redirect(string $url, array $response = null): RedirectResponse
    {
        return redirect($url)->with($response);
    }
}
