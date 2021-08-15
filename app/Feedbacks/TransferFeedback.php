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
                'message' => __(
                    'Transfer :action successfully.', [
                        'action' => __($performedAction),
                    ]
                ),
                'status' => 'success',
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
                __('You don\'t have any wallet connected to you account. Transfers feature is not available.'),
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
        return ['status' => self::messageWithLink(
            __('You don\'t have any wallet marked as active. Transfer of sums is not available.'),
            route('wallet.view.all'),
            __('Activate a wallet by clicking here.')
        ),
            'status_type' => 'primary',];
    }
}
