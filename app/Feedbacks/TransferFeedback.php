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
}
