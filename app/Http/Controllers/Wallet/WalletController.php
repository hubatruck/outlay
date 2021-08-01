<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Editor view name
     *
     * @var string
     */
    private $editorViewName = 'wallet/edit';

    /**
     * Show the view for editing wallet
     *
     * @return View
     */
    public function createView(): View
    {
        return view($this->editorViewName);
    }

    /**
     * Show the view for editing wallet
     *
     * @param string $id
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function editView(string $id)
    {
        $wallet = Wallet::find($id);
        if (empty($wallet)) {
            return $this->walletDoesNotExist();
        }
        if ($wallet->user_id !== (Auth::user()->id ?? '-1')) {
            return $this->cannotEditWallet();
        }
        return view($this->editorViewName, compact('wallet'));
    }

    /**
     * Redirect user to wallet list if wallet does not exist
     *
     * @return RedirectResponse
     */
    private function walletDoesNotExist(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Wallet does not exist.'),
                'status' => 'danger'
            ]);
    }

    /**
     * Redirect user to wallet list, if user cannot edit wallet
     *
     * @return RedirectResponse
     */
    private function cannotEditWallet(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('You cannot edit this wallet.'),
                'status' => 'danger'
            ]);
    }

    /**
     * Save a new wallet
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeWallet(Request $request): RedirectResponse
    {
        $newWalletData = $this->validateRequest($request);
        $newWalletData['user_id'] = Auth::user()->id;
        Wallet::create($newWalletData);

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

    /**
     * Redirect user to wallet list with success message
     *
     * @param string $successMethod
     * @return RedirectResponse
     */
    private function redirectSuccess(string $successMethod = 'created'): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Wallet :action successfully.', ['action' => __($successMethod)]),
                'status' => 'success'
            ]);
    }

    /**
     * Update a wallet
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateWallet(Request $request, string $id): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $wallet = Wallet::find($id);

        if (empty($wallet)) {
            return $this->walletDoesNotExist();
        }
        if ($wallet->user_id !== (Auth::user()->id ?? '-1')) {
            return $this->cannotEditWallet();
        }

        $wallet->fill($validated);
        $wallet->save();
        return $this->redirectSuccess('updated');
    }
}
