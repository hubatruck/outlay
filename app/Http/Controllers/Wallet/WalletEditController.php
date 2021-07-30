<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletEditController extends Controller
{
    private $viewName = 'wallet/edit';

    public function createView(): View
    {
        return view($this->viewName);
    }

    /// https://stackoverflow.com/a/59745972
    public function editView(string $id)
    {
        $wallet = Wallet::find($id);
        if (empty($wallet)) {
            return $this->walletDoesNotExist();
        }
        if ($wallet->user_id !== Auth::user()->id) {
            return $this->cannotEditWallet();
        }
        return view($this->viewName, compact('wallet'));
    }

    public function storeWallet(Request $request): RedirectResponse
    {
        $newWalletData = $this->validateRequest($request);
        $newWalletData['user_id'] = Auth::user()->id;
        Wallet::create($newWalletData);

        return $this->redirectSuccess();
    }

    public function updateWallet(Request $request, string $id): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $wallet = Wallet::find($id);

        if (empty($wallet)) {
            return $this->walletDoesNotExist();
        }
        if ($wallet->user_id !== Auth::user()->id) {
            return $this->cannotEditWallet();
        }

        $wallet->fill($validated);
        $wallet->save();
        return $this->redirectSuccess('updated');
    }

    private function cannotEditWallet(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('You cannot edit this wallet.'),
                'status' => 'danger'
            ]);
    }

    private function walletDoesNotExist(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Wallet does not exist.'),
                'status' => 'danger'
            ]);
    }

    private function redirectSuccess(string $successMethod = 'created'): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Wallet :action successfully.', ['action' => __($successMethod)]),
                'status' => 'success'
            ]);
    }

    public function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'user_id' => 'integer',
            'name' => 'required|max:255',
            'notes' => 'nullable|string',
            'balance' => 'numeric',
            'is_card' => 'nullable',
        ]);

        $data['is_card'] = isset($data['is_card']);
        return $data;
    }
}
