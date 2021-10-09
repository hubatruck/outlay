<?php

namespace App\Feedbacks;

use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;

class WalletFeedback
{
    public const WALLET_VIEW_ALL = 'wallet.view.all';

    /**
     * User cannot view the wallet.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function viewError(): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('You cannot view this wallet.'),
            'type' => 'danger',
        ]);
        return redirect(route(self::WALLET_VIEW_ALL));
    }

    /**
     * Redirect user to wallet list if wallet does not exist.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function existError(): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Wallet does not exist.'),
            'type' => 'danger',
        ]);
        return redirect()->route(self::WALLET_VIEW_ALL);
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
        addSessionMsg([
            'content' => __('Error') . ': ' . __(
                    'No :type wallet linked to your account found.', [
                        'type' => __($type),
                    ]
                ),
            'type' => 'danger',
        ]);
        return redirect($url);
    }

    /**
     * Wallet cannot be used for quick transaction creation.
     * Type: error
     *
     * @param string $targetType
     * @return RedirectResponse
     */
    public static function quickCreateError(string $targetType): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Wallet unavailable for quick :target creation.', ['target' => __($targetType)]),
            'type' => 'danger',
        ]);
        return redirect(previousUrlOr(route(self::WALLET_VIEW_ALL)));
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
        return self::hasError($wallet, 'transactions');
    }

    /**
     * Wallet cannot be deleted because has got $targetType tied to it
     * Type: error
     *
     * @param Wallet $wallet
     * @param $targetType
     * @return RedirectResponse
     */
    public static function hasError(Wallet $wallet, $targetType): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Wallet has :target linked to it. Cannot be deleted.', ['target' => __($targetType)]),
            'type' => 'danger',
        ]);
        return redirect(previousUrlOr(route('wallet.view.details', ['id' => $wallet->id])));
    }

    /**
     * Wallet got transfers
     * Type: error
     *
     * @param Wallet $wallet
     * @return RedirectResponse
     */
    public static function hasTransfersError(Wallet $wallet): RedirectResponse
    {
        return self::hasError($wallet, 'transfers');
    }

    /**
     * Redirect user to wallet list, if user cannot edit wallet.
     * Type: error
     *
     * @return RedirectResponse
     */
    public static function editError(): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('You cannot edit this wallet.'),
            'type' => 'danger',
        ]);
        return redirect(previousUrlOr(route(self::WALLET_VIEW_ALL)));
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
        addSessionMsg([
            'content' => __(
                'Wallet :action successfully.', [
                    'action' => __($successMethod),
                ]
            ),
            'type' => 'success',
        ]);
        return redirect($url ?? route(self::WALLET_VIEW_ALL));
    }
}
