<?php

namespace App\Http\Controllers\Wallet;

use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This controller handles wallet modifying related requests
 */
class WalletDataController extends Controller
{
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

        return WalletFeedback::success();
    }

    /**
     * Validate request data
     *
     * @param Request $request
     * @param bool $isNewModelInstance
     * @return array
     */
    public function validateRequest(Request $request, bool $isNewModelInstance = true): array
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'notes' => 'nullable|string',
            'balance' => ($isNewModelInstance ? '' : 'nullable|') . 'numeric|max:999999.99',
            'is_public' => 'nullable',
        ]);

        $data['is_public'] = isset($data['is_public']);
        return $data;
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
        $validated = $this->validateRequest($request, false);

        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = Wallet::check($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $wallet->fill($validated);
        $wallet->save();
        return WalletFeedback::success('updated', route('wallet.view.details', ['id' => $id]));
    }

    /**
     * Delete a wallet if it does not have transactions tied to it
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function deleteWallet(string $id): RedirectResponse
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = Wallet::check($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $hasTransactions = $wallet->hasTransactions();
        if ($hasTransactions || $wallet->hasTransfers()) {
            return $hasTransactions
                ? WalletFeedback::hasTransactionsError($wallet)
                : WalletFeedback::hasTransfersError($wallet);
        }

        $wallet->forceDelete();
        return WalletFeedback::success('deleted');
    }

    /**
     * Toggle the trashed/active status of a wallet
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function toggleHidden(string $id): RedirectResponse
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = Wallet::check($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        if ($wallet->trashed()) {
            $action = 'restored';
            $wallet->restore();
        } else {
            $action = 'hidden';
            $wallet->delete();
        }

        return WalletFeedback::success(
            $action,
            previousUrlOr(route('wallet.view.details', ['id' => $id]))
        );
    }
}
