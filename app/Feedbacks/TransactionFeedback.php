<?php

namespace App\Feedbacks;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class TransactionFeedback
{
    public const TRANSACTION_VIEW_ALL = 'transaction.view.all';

    /**
     * Redirect user with success message
     *
     * @param string $performedAction
     * @return RedirectResponse
     */
    public static function success(string $performedAction = 'created'): RedirectResponse
    {
        return redirect(route(self::TRANSACTION_VIEW_ALL))
            ->with([
                'status' => __(
                    'Transaction :action successfully.', [
                        'action' => __($performedAction),
                    ]
                ),
                'status_type' => 'success',
            ]);
    }

    /**
     * Redirect user with 'transaction does not exist' error
     *
     * @return RedirectResponse
     */
    public static function existError(): RedirectResponse
    {
        return redirect(route(self::TRANSACTION_VIEW_ALL))
            ->with([
                'status' => __('Error') . ': ' . __('Transaction does not exist.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Redirect user with 'cannot edit this transaction' error
     *
     * @return Application|RedirectResponse|Redirector
     */
    public static function editError()
    {
        return redirect(route(self::TRANSACTION_VIEW_ALL))
            ->with([
                'status' => __('Error') . ': ' . __('You cannot edit this transaction.'),
                'status_type' => 'danger',
            ]);
    }

    /**
     * Alert for cases when the user does not have a wallet
     *
     * @return array
     */
    public static function noWalletMsg(): array
    {
        return [
            'status' => self::messageWithLink(
                __('You don\'t have any wallet connected to you account. Transactions feature is not available.'),
                route('wallet.view.create'),
                __('Create a wallet by clicking here.')
            ),
            'status_type' => 'primary',
        ];
    }

    /**
     * @param string $mainMsg Primary message
     * @param string $link Link for create action
     * @param string $createMsg Create action message
     * @return string
     */
    private static function messageWithLink(string $mainMsg, string $link, string $createMsg): string
    {
        return $mainMsg . '<br><a class="uk-link" href="' . $link . '">' . $createMsg . '</a>';
    }

    /**
     * Alert for cases when the user does not have an active wallet
     *
     * @return array
     */
    public static function noActiveWalletMsg(): array
    {
        return [
            'status' => self::messageWithLink(
                __('You don\'t have any wallet marked as active. Transaction creation is unavailable.'),
                route('wallet.view.all'),
                __('Activate a wallet by clicking here.')
            ),
            'status_type' => 'primary',
        ];
    }
}
