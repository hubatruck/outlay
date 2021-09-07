<?php

namespace App\Http\Controllers\Transfer;

use App\DataTables\TransfersDataTable;
use App\Feedbacks\TransferFeedback;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * This controller handles transfer view related requests
 */
class TransferViewController extends Controller
{
    /**
     * Show all transfers for the user
     *
     * @param TransfersDataTable $dataTable
     * @return mixed
     */
    public function listView(TransfersDataTable $dataTable): mixed
    {
        if (!Auth::user()->hasWallet()) {
            addSessionMsg(TransferFeedback::noWalletMsg(), true);
        } else if (!Auth::user()->hasAnyActiveWallet()) {
            addSessionMsg(TransferFeedback::noActiveWalletMsg(), true);
        }

        return $dataTable->render('transfer/list');
    }

    /**
     * Show the create a transfer view
     *
     * @param Request $request
     * @return View|Factory|RedirectResponse|Application
     */
    public function createView(Request $request): View|Factory|RedirectResponse|Application
    {
        $fromWalletID = $request->get('from_wallet');
        $toWalletID = $request->get('to_wallet');
        $fromWalletCheck = $this->quickCreateWalletCheck($fromWalletID);
        $toWalletCheck = $this->quickCreateWalletCheck($toWalletID, false);
        if ($toWalletCheck || $fromWalletCheck) {
            return $toWalletCheck ?? $fromWalletCheck;
        }

        if (!Auth::user()->hasAnyActiveWallet()) {
            return WalletFeedback::noWalletError(
                Auth::user()->hasWallet() ? 'active' : '',
                route('transfer.view.all')
            );
        }

        addSessionMsg(TransferFeedback::warnIrreversibleTransfer(), true);
        return view('transfer.create', [
            'selected_from_wallet_id' => $fromWalletID ?? '-1',
            'selected_to_wallet_id' => $toWalletID ?? '-1',
        ]);
    }

    /**
     * Check if a wallet can be used for quick transfer creation
     *
     * @param string|null $walletID
     * @param bool $checkOwner Check if user owns the wallet
     * @return RedirectResponse|null
     */
    private function quickCreateWalletCheck(string $walletID = null, bool $checkOwner = true): ?RedirectResponse
    {
        if ($walletID !== null) {
            $wallet = Wallet::find($walletID);

            $ownership = $checkOwner && !Auth::user()->owns($wallet);
            if ($wallet === null || $wallet->trashed() || $ownership) {
                return WalletFeedback::quickCreateError('transfer');
            }
        }
        return null;
    }
}
