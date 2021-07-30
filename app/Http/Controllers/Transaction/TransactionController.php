<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    private $viewName = 'transaction/edit';

    public function createView(): View
    {
        return view($this->viewName);
    }

    /// https://stackoverflow.com/a/59745972
    public function editView(string $id)
    {
        $transaction = Transaction::find($id);
        $wallet = $transaction->wallet;
        if (empty($transaction) || empty($wallet)) {
            return $this->transactionDoesNotExist();
        }
        if ($wallet->user_id !== Auth::user()->id) {
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

        if (empty($transaction)) {
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
        ]);

        $data['is_card'] = isset($data['is_card']);
        return $data;
    }
}
