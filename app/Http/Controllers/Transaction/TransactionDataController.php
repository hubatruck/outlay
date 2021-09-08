<?php

namespace App\Http\Controllers\Transaction;

use App\Feedbacks\TransactionFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletIsActiveRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * This controller handles wallet modifying related requests
 */
class TransactionDataController extends Controller
{
    public function testStore(Request $request): RedirectResponse
    {
        $validatedData = $this->testValidateRequest($request);

        $sharedProps = array_filter($validatedData, static function ($value) {
            return !is_array($value);
        });

        $newTransactions = [];
        foreach ($validatedData['scope'] as $key => $scope) {
            $newTransactions[] = array_merge([
                'scope' => $scope,
                'amount' => $validatedData['amount'][$key],
            ], $sharedProps);
        }

        Transaction::insert($newTransactions);
        return TransactionFeedback::success();
    }

    public function testValidateRequest(Request $request, bool $walletMustBeActive = true): array
    {
        $walletRules = ['bail'];
        if ($walletMustBeActive) {
            $walletRules[] = new UserOwnsWalletRule();
            $walletRules[] = new WalletIsActiveRule();
            $walletRules[] = Auth::user()->hasAnyActiveWallet() ? 'required' : 'nullable';
        }

        return $request->validate([
            'scope' => 'required|array|min:1',
            'scope.*' => 'required|string|max:255',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric|min:0.01|max:999999.99',
            'wallet_id' => $walletRules,
            'transaction_type_id' => [
                'required',
                Rule::in(TransactionType::all()->pluck('id')->toArray()),
            ],
            'transaction_date' => 'required|date|date_format:' . globalDateFormat(),
        ]);
    }

    /**
     * Store a transaction in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
}
