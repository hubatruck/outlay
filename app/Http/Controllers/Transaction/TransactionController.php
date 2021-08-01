<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private $viewName = 'transaction/edit';

    public function createView()
    {
        if (!count(Auth::user()->wallets)) {
            return $this->noWallet();
        }
        return view($this->viewName);
    }

    /// https://stackoverflow.com/a/59745972
    public function editView(string $id)
    {
        if (!count(Auth::user()->wallets)) {
            return $this->noWallet();
        }

        $transaction = Transaction::find($id);
        if (empty($transaction) || empty($transaction->wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($transaction->wallet->user_id !== Auth::user()->id) {
            return $this->cannotEditTransaction();
        }
        return view($this->viewName, compact('transaction'));
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $newTransactionData = $this->validateRequest($request);
        Transaction::create($newTransactionData);

        return $this->redirectSuccess();
    }

    public function updateTransaction(Request $request, string $id): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $transaction = Transaction::find($id);

        if (empty($transaction) || empty($transaction->wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($transaction->wallet->user_id !== Auth::user()->id) {
            return $this->cannotEditTransaction();
        }

        $transaction->fill($validated);
        $transaction->save();
        return $this->redirectSuccess('updated');
    }

    private function cannotEditTransaction(): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('You cannot edit this transaction.'),
                'status' => 'danger'
            ]);
    }

    private function transactionDoesNotExist(): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('Transaction does not exist.'),
                'status' => 'danger'
            ]);
    }

    public function noWallet(): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('No wallet linked to account found.'),
                'status' => 'danger'
            ]);
    }

    private function redirectSuccess(string $successMethod = 'created'): RedirectResponse
    {
        return redirect()
            ->route('transaction.view.all')
            ->with([
                'message' => __('Transaction :action successfully.', ['action' => __($successMethod)]),
                'status' => 'success'
            ]);
    }

    public function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'wallet_id' => 'required|integer',
            'scope' => 'required|max:255',
            'amount' => 'numeric',
            'transaction_type_id' => 'required|integer',
            'transaction_date' => 'required|date|date_format:Y-m-d',
        ]);

        return $data;
    }
}
