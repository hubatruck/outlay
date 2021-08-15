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
}
