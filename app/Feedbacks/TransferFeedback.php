<?php

namespace App\Feedbacks;

use Illuminate\Http\RedirectResponse;

class TransferFeedback
{

    /**
     * Redirect user with success message
     *
     * @param string $performedAction
     * @return RedirectResponse
     */
    public static function success(string $performedAction = 'created'): RedirectResponse
    {
        return redirect(route('transfer.view.all'))
            ->with([
                'content' => __(
                    'Transfer :action successfully.', [
                        'action' => __($performedAction),
                    ]
                ),
                'type' => 'success',
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
            'content' => self::messageWithLink(
                __(
                    'You don\'t have any wallet connected to you account. :feature is unavailable.',
                    ['feature' => __('Transfers feature')]
                ),
                route('wallet.view.create'),
                __('Create a wallet by clicking here.')
            ),
            'type' => 'primary',
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
            'content' => self::messageWithLink(
                __(
                    'You don\'t have any wallet marked as active. :feature is unavailable.',
                    ['feature' => __('Transfer of funds')]
                ),
                route('wallet.view.all'),
                __('Activate a wallet by clicking here.')
            ),
            'type' => 'primary',
        ];
    }

    /**
     * Warn about transfers not being irreversible
     *
     * @return string[]
     */
    public static function warnIrreversibleTransfer(): array
    {
        return [
            'content' =>
                '<strong>' . __('Warning') . '</strong>: '
                . __('Please be careful when selecting the destination wallet. Sending sums to wallets that have a name next to them and marked \'External\' is irreversible, as those belong to other users.'),
            'type' => 'warning',
        ];
    }
}
