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
     * @param int $itemCount Count of new transactions
     * @return RedirectResponse
     */
    public static function success(string $performedAction = 'created', int $itemCount = 1): RedirectResponse
    {
        addSessionMsg([
            'content' => trans_choice(
                'Transaction :action successfully.|Transactions :action successfully.',
                $itemCount,
                ['action' => __($performedAction),]
            ),
            'type' => 'success',
        ]);
        return redirect(route(self::TRANSACTION_VIEW_ALL));
    }

    /**
     * Redirect user with 'transaction does not exist' error
     *
     * @return RedirectResponse
     */
    public static function existError(): RedirectResponse
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Transaction does not exist.'),
            'type' => 'danger',
        ]);
        return redirect(route(self::TRANSACTION_VIEW_ALL));
    }

    /**
     * Redirect user with 'cannot edit this transaction' error
     *
     * @return Redirector|RedirectResponse|Application
     */
    public static function editError(): Redirector|RedirectResponse|Application
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('You cannot edit this transaction.'),
            'type' => 'danger',
        ]);
        return redirect(route(self::TRANSACTION_VIEW_ALL));
    }

    /**
     * The user hasn't provided any items (amount+scope) for the transaction
     * Type: Error
     *
     * @return Application|RedirectResponse|Redirector
     */
    public static function checkItemError(): Application|RedirectResponse|Redirector
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Please add at least one correct item to the transaction.'),
            'type' => 'danger',
        ]);

        return redirect(route('transaction.view.create.items'));
    }

    /**
     * The user hasn't provided any payment details for the transaction
     * Type: Error
     *
     * @return Application|RedirectResponse|Redirector
     */
    public static function checkPaymentError(): Application|RedirectResponse|Redirector
    {
        addSessionMsg([
            'content' => __('Error') . ': ' . __('Please fill out the payment details correctly.'),
            'type' => 'danger',
        ]);

        return redirect(route('transaction.view.create.payment'));
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
                    ['feature' => __('Transactions feature')],
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
                    ['feature' => __('Transaction creation')]
                ),
                route('wallet.view.all'),
                __('Activate a wallet by clicking here.')
            ),
            'type' => 'primary',
        ];
    }
}
