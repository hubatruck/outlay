<?php

namespace App\Http\Controllers\Transfer;

use App\Feedbacks\TransferFeedback;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletExistsRule;
use App\Rules\WalletIsActiveRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * This controller handles transfer modifying related requests
 */
class TransferDataController extends Controller
{
    /**
     * Store a transfer in the database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
            'from_wallet_id' => ['required', new UserOwnsWalletRule(), new WalletIsActiveRule()],
            'to_wallet_id' => ['required', 'different:from_wallet_id', new WalletExistsRule()],
            'amount' => 'numeric|min:0.01|max:999999.99',
            'transfer_date' => 'required|date|date_format:' . globalDateFormat() . '|before_or_equal:' . date(globalDateFormat()),
        ]);
    }
}
