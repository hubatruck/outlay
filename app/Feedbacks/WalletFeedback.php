<?php

namespace App\Feedbacks;

use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;

class WalletFeedback
{
    /**
     * User cannot view the wallet.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function viewError(): RedirectResponse
    {
        return redirect(route('wallet.view.all'))
            ->with([
                'status' => __('Error') . ': ' . __('You cannot view this wallet.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Redirect user to wallet list if wallet does not exist.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function existError(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'status' => __('Error') . ': ' . __('Wallet does not exist.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Redirect user with 'no wallet found' error.
     * Type: error
     *
     * @param string $type
     * @param null $url
     * @return RedirectResponse
     */
    public static function noWalletError(string $type = '', $url = null): RedirectResponse
    {
        $url = $url ?? previousUrlOr(route('transaction.view.all'));
        return redirect($url)
            ->with([
                'status' => __('Error') . ': ' . __(
                        'No :type wallet linked to your account found.', [
                            'type' => __($type),
                        ]
                    ),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Wallet cannot be used for quick transaction creation.
     * Type: error
     * @return RedirectResponse
     */
    public static function quickCreateError(): RedirectResponse
    {
        return redirect(route('wallet.view.all'))
            ->with([
                'status' => __('Error') . ': ' . __('Wallet unavailable for quick transaction creation.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Wallet got transactions
     * Type: error
     *
     * @param Wallet $wallet
     * @return RedirectResponse
     */
    public static function hasTransactionsError(Wallet $wallet): RedirectResponse
    {
        return redirect(previousUrlOr(route('wallet.view.details', ['id' => $wallet->id])))
            ->with([
                'status' => __('Error') . ': ' . __('Wallet has transactions linked to it. Cannot be deleted.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Redirect user to wallet list, if user cannot edit wallet.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function editError(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'status' => __('Error') . ': ' . __('You cannot edit this wallet.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Redirect user to wallet list with success message.
     * Type: success
     *
     * @param string $successMethod
     * @param string|null $url
     * @return RedirectResponse
     */
    public static function success(string $successMethod = 'created', string $url = null): RedirectResponse
    {
        return redirect($url ?? route('wallet.view.all'))
            ->with([
                'status' => __(
                    'Wallet :action successfully.', [
                        'action' => __($successMethod),
                    ]
                ),
                'status_type' => 'success',
            ]);
    }
}
