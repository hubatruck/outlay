<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private $viewName = 'transaction/edit';

    /**
     * Show the view for creating a transaction
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function createView()
    {
        if (!count(Auth::user()->wallets ?? null)) {
            return $this->noWallet();
        }
        return view($this->viewName);
    }

    /**
     * Redirect user with 'no wallet found' error
     *
     * @return RedirectResponse
     */
    public function noWallet(): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('No wallet linked to account found.'),
                'status' => 'danger'
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
        if (empty($transaction) || empty($transaction->wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($transaction->wallet->user_id !== (Auth::user()->id ?? -1)) {
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
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('Transaction does not exist.'),
                'status' => 'danger'
            ]);
    }

    /**
     * Redirect user with 'cannot edit this transaction' error
     *
     * @return RedirectResponse
     */
    private function cannotEditTransaction(): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('You cannot edit this transaction.'),
                'status' => 'danger'
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
     * @return array
     */
    public function validateRequest(Request $request): array
    {
        return $request->validate([
            'wallet_id' => 'required|integer',
            'scope' => 'required|max:255',
            'amount' => 'numeric|max:999999.99',
            'transaction_type_id' => 'required|integer',
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
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('Transaction :action successfully.', ['action' => __($successMethod)]),
                'status' => 'success'
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
        $validated = $this->validateRequest($request);

        $transaction = Transaction::find($id);

        if (empty($transaction) || empty($transaction->wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($transaction->wallet->user_id !== (Auth::user()->id ?? -1)) {
            return $this->cannotEditTransaction();
        }

        $transaction->fill($validated);
        $transaction->save();
        return $this->redirectSuccess('updated');
    }

    /**
     * Delete a transaction from the database
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function deleteTransaction(Request $request, string $id): RedirectResponse
    {
        $transaction = Transaction::find($id);

        if (empty($transaction) || empty($transaction->wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($transaction->wallet->user_id !== (Auth::user()->id ?? -1)) {
            return $this->cannotEditTransaction();
        }

        $transaction->delete();
        return $this->redirectSuccess('removed');
    }
}
