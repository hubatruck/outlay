<?php

namespace App\Http\Controllers\Transaction;

use App\Feedbacks\TransactionFeedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionMultiStoreRequest;
use App\Http\Validators\TransactionValidator;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
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

        if ($request->session()->has('transaction')) {
            $partialData = array_merge($request->session()->get('transaction'), $validatedData);
        } else {
            $partialData = $validatedData;
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
     * @param TransactionMultiStoreRequest $request
     * @return Factory|View|Redirector|Application|RedirectResponse
     */
    public function store(TransactionMultiStoreRequest $request): Factory|View|Redirector|Application|RedirectResponse
    {
        /// Note: Data is already validated
        $transactionData = $request->all();
        $sharedProps = array_filter($transactionData, static function ($value) {
            return !\is_array($value);
        });

        $newTransactions = [];
        $now = Carbon::now()->toDateTimeString();

        foreach ($transactionData['scope'] as $key => $scope) {
            $newTransactions[] = array_merge([
                'scope' => $scope,
                'amount' => (integer)($transactionData['amount'][$key] * 100),
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
        $response = null;

        $oldTransaction = Transaction::find($id);
        $permissionCheck = Transaction::checkStatus($oldTransaction);
        if ($permissionCheck !== null) {
            $response = $permissionCheck;
        } else {
            $wallet_id = $request->get('wallet_id');
            $validationType = $wallet_id && ((string) $oldTransaction->wallet_id !== (string) $wallet_id)
                ? TransactionValidator::EVERYTHING_REG
                : TransactionValidator::EVERYTHING_NO_ACTIVE_WALLET;
            $validated = TransactionValidator::validate($request, $validationType);

            $updatedTransaction = new Transaction($validated);
            if ($updatedTransaction->wallet && !Auth::user()->owns($updatedTransaction)) {
                $response = TransactionFeedback::editError();
            } else {
                $oldTransaction->fill($updatedTransaction->attributesToArray());
                $oldTransaction->save();
                $response = TransactionFeedback::success('updated');
            }
        }

        return $response;
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
