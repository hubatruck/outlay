<?php

namespace App\Http\Controllers\Transaction;

use App\Feedbacks\TransactionFeedback;
use App\Http\Controllers\Controller;
use App\Http\Validators\TransactionValidator;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return $this->partialDataStore(
            $request,
            TransactionValidator::ONLY_ITEM_ARR,
            'transaction.view.create.payment'
        );
    }

    /**
     * Store partial transaction data in the session
     *
     * @param Request $request
     * @param int $validationType
     * @param string $redirectRoute
     * @return RedirectResponse
     * @see TransactionValidator for more info
     */
    private function partialDataStore(Request $request, int $validationType, string $redirectRoute): RedirectResponse
    {
        $validatedData = TransactionValidator::validate($request, $validationType);

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
        return $this->partialDataStore(
            $request,
            TransactionValidator::ONLY_PAYMENT,
            'transaction.view.create.overview'
        );
    }

    /**
     * Store the transaction(s) in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = TransactionValidator::validate($request, TransactionValidator::EVERYTHING_WITH_ITEMS);

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
        $validationType = $wallet_id && ((string) $oldTransaction->wallet_id !== (string) $wallet_id)
            ? TransactionValidator::EVERYTHING_REG
            : TransactionValidator::EVERYTHING_NO_ACTIVE_WALLET;
        $validated = TransactionValidator::validate($request, $validationType);

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
