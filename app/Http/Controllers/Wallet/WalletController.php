<?php

namespace App\Http\Controllers\Wallet;

use App\Feedbacks\WalletFeedback;
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
    private string $editorViewName = 'wallet/edit';

    /**
     * List all wallets view
     *
     * @return Factory|\Illuminate\Contracts\View\View|Application
     */
    public function index(): Factory|\Illuminate\Contracts\View\View|Application
    {
        $wallets = Auth::user()->wallets()->get()->sortBy('deleted_at');
        return view('wallet.list', compact('wallets'));
    }

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
     * @return \Illuminate\Contracts\View\View|Factory|RedirectResponse|Application
     */
    public function editView(string $id): \Illuminate\Contracts\View\View|Factory|RedirectResponse|Application
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = Wallet::check($wallet);
        return $permissionCheck ?: view($this->editorViewName, compact('wallet'));
    }

    /**
     * Show details page for wallet, if user owns it
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View|Factory|RedirectResponse|Application
     */
    public function detailsView(string $id): \Illuminate\Contracts\View\View|Factory|RedirectResponse|Application
    {
        $wallet = Wallet::withTrashed()->findOrFail($id);
        if (!Auth::user()->owns($wallet)) {
            return WalletFeedback::viewError();
        }

        $activity = (int) $wallet->hasTransfers() + (int) $wallet->hasTransactions();
        if ($activity === 1) {
            addSessionMsg(WalletFeedback::partialActivity(), true);
        } else if ($activity === 0) {
            addSessionMsg(WalletFeedback::noActivity(), true);
        }

        return view('wallet.details', compact('wallet'));
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
            'is_card' => 'nullable',
        ]);

        $data['is_card'] = isset($data['is_card']);
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
