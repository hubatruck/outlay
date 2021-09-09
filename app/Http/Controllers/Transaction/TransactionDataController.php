<?php

namespace App\Http\Controllers\Transaction;

use App\Feedbacks\TransactionFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletIsActiveRule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * This controller handles wallet modifying related requests
 */
class TransactionDataController extends Controller
{
    /**
     * Stores the items (in the session) for a transaction
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeItems(Request $request): RedirectResponse
    {
        return $this->partialDataStore($request, 'validateItems', 'transaction.view.create.payment');
    }

    /**
     * Store partial transaction data in the session
     *
     * @param Request $request
     * @param string $validator
     * @param string $redirectRoute
     * @return RedirectResponse
     */
    private function partialDataStore(Request $request, string $validator, string $redirectRoute): RedirectResponse
    {
        $validatedData = $this->$validator($request);
        if (!$request->session()->has('transaction')) {
            $partialData = $validatedData;
        } else {
            $partialData = array_merge($request->session()->get('transaction'), $validatedData);
        }
        $request->session()->put('transaction', $partialData);

        return redirect()->route($redirectRoute);
    }

    /**
     * Store the payment details of the transaction
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storePayment(Request $request): RedirectResponse
    {
        return $this->partialDataStore($request, 'validatePayment', 'transaction.view.create.overview');
    }

    /**
     * Store the transaction(s) in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validatePayment($request);

        $sharedProps = array_filter($validatedData, static function ($value) {
            return !is_array($value);
        });

        $newTransactions = [];
        $now = Carbon::now()->toDateTimeString();

        $partialTransactionData = $request->session()->get('transaction');
        if (empty($partialTransactionData['scope']) || empty($partialTransactionData['amount'])) {
            $request->session()->put('transaction', $validatedData);
            return TransactionFeedback::noItemError();
        }

        foreach ($partialTransactionData['scope'] as $key => $scope) {
            $newTransactions[] = array_merge([
                'scope' => $scope,
                'amount' => $partialTransactionData['amount'][$key],
                'created_at' => $now,
                'updated_at' => $now,
            ], $sharedProps);
        }

        Transaction::insert($newTransactions);
        $request->session()->forget(keys: 'transaction');
        return TransactionFeedback::success('created', sizeof($newTransactions));
    }

    /**
     * Store a transaction in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function _store(Request $request): RedirectResponse
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
    public function update(Request $request, string $id): RedirectResponse
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
    public function delete(string $id): RedirectResponse
    {
        $transaction = Transaction::find($id);

        $permissionCheck = Transaction::checkStatus($transaction);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $transaction->delete();
        return TransactionFeedback::success('removed');
    }

    /**
     * Validate transaction items
     *
     * @param Request $request
     * @return array
     */
    private function validateItems(Request $request): array
    {
        return $request->validate([
            'scope' => 'required|array|min:1',
            'scope.*' => 'required|string|max:255',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0.01|max:999999.99',
        ]);
    }

    /**
     * Validate payment details for a transaction
     *
     * @param Request $request
     * @param bool $walletMustBeActive
     * @return array
     */
    private function validatePayment(Request $request, bool $walletMustBeActive = true): array
    {
        $walletRules = ['bail'];
        if ($walletMustBeActive) {
            $walletRules[] = new UserOwnsWalletRule();
            $walletRules[] = new WalletIsActiveRule();
            $walletRules[] = Auth::user()->hasAnyActiveWallet() ? 'required' : 'nullable';
        }

        return $request->validate([
            'wallet_id' => $walletRules,
            'transaction_type_id' => [
                'required',
                Rule::in(TransactionType::all()->pluck('id')->toArray()),
            ],
            'transaction_date' => 'required|date|date_format:' . globalDateFormat(),
        ]);
    }
}
