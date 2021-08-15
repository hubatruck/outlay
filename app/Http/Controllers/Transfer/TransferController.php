<?php

namespace App\Http\Controllers\Transfer;

use App\DataTables\TransfersDataTable;
use App\Feedbacks\TransferFeedback;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletAvailable;
use App\Rules\WalletExistsRule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    /**
     * Show all transfers for the user
     *
     * @param TransfersDataTable $dataTable
     * @return mixed
     */
    public function index(TransfersDataTable $dataTable)
    {
        $messages = [];
        if (!Auth::user()->hasWallet()) {
            $messages[] = TransferFeedback::noWalletMsg();
        } else if (!Auth::user()->hasAnyActiveWallet()) {
            $messages[] = TransferFeedback::noActiveWalletMsg();
        }

        session(['status' => $messages]);
        return $dataTable->render('transfer/list',);
    }

    /**
     * Show the create a transfer view
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function createView()
    {
        if (!Auth::user()->hasAnyActiveWallet()) {
            return WalletFeedback::noWalletError(
                Auth::user()->hasWallet() ? 'active' : '',
                route('transfer.view.all')
            );
        }
        return view('transfer.create');
    }

    /**
     * Store a transfer in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $transfer = $this->validateRequest($request);
        Transfer::create($transfer);

        return TransferFeedback::success();
    }

    /**
     * Validate request data
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'description' => 'required|string|max:255',
            'from_wallet_id' => ['required', new UserOwnsWalletRule(), new WalletAvailable()],
            'to_wallet_id' => ['required', 'different:from_wallet_id', new WalletExistsRule()],
            'amount' => 'numeric|min:0.01|max:999999.99',
            'transfer_date' => 'required|date|date_format:Y-m-d|before_or_equal:' . date('Y-m-d'),
        ]);
    }
}
