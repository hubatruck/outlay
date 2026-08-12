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
     */
    public function list(TransfersDataTable $dataTable): mixed
    {
        if (! Auth::user()->hasWallet()) {
            addSessionMsg(TransferFeedback::noWalletMsg(), true);
        } elseif (! Auth::user()->hasAnyActiveWallet()) {
            addSessionMsg(TransferFeedback::noActiveWalletMsg(), true);
        }

        return $dataTable->render('transfer/list');
    }

    /**
     * Show the create a transfer view
     */
    public function create(Request $request): View|Factory|RedirectResponse|Application
    {
        $response = null;

        $fromWalletID = $request->get('from_wallet');
        $toWalletID = $request->get('to_wallet');
        $fromWalletCheck = $this->quickCreateWalletCheck($fromWalletID);
        $toWalletCheck = $this->quickCreateWalletCheck($toWalletID, false);

        if ($toWalletCheck || $fromWalletCheck) {
            $response = $toWalletCheck ?? $fromWalletCheck;
        } elseif (Auth::user()->hasAnyActiveWallet()) {
            addSessionMsg(TransferFeedback::warnIrreversibleTransfer(), true);
            $response = view('transfer.create', [
                'selected_from_wallet_id' => $fromWalletID ?? '-1',
                'selected_to_wallet_id' => $toWalletID ?? '-1',
            ]);
        } else {
            $response = WalletFeedback::noWalletError(
                Auth::user()->hasWallet() ? 'active' : '',
                route('transfer.view.all')
            );
        }

        return $response;
    }

    /**
     * Check if a wallet can be used for quick transfer creation
     *
     * @param  bool  $checkOwnership  Check if user owns the wallet
     */
    private function quickCreateWalletCheck(?string $walletID = null, bool $checkOwnership = true): ?RedirectResponse
    {
        if ($walletID !== null) {
            $wallet = Wallet::find($walletID);

            $ownership = $checkOwnership && ! Auth::user()->owns($wallet);
            if ($wallet === null || $ownership || $wallet->trashed()) {
                return WalletFeedback::quickCreateError('transfer');
            }
        }

        return null;
    }
}
