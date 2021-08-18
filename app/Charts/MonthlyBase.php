<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonthlyBase
{
    /**
     * Colors used for the chart
     *
     * @var string[]
     */
    public static array $colors = [
        '#008FFB', '#00E396', '#feb019', '#ff455f', '#775dd0', '#80effe',
        '#0077B5', '#ff6384', '#c9cbcf', '#0057ff', '#00a9f4', '#2ccdc9', '#5e72e4',
    ];

    /**
     * Generate a base query that can be used for charts
     *
     * @param string $walletID
     * @return Builder
     */
    protected function getBaseQuery(string $walletID): Builder
    {
        return Transaction::with(['transactionType', 'wallet'])
            ->thisMonth($this->lastDate())
            ->join('wallets', 'wallet_id', '=', 'wallets.id')
            ->join(
                'transaction_types',
                'transaction_type_id',
                '=',
                'transaction_types.id'
            )
            ->whereIn('wallet_id', function ($query) {
                /// https://stackoverflow.com/a/16815955
                $query->select('id')->from('wallets')
                    ->where('user_id', '=', Auth::user()->id ?? -1);
            })
            ->where('wallet_id', '=', $walletID);
    }

    /**
     * Which day should be the last displayed for current month
     *
     * @return string
     */
    protected function lastDate(): string
    {
        return date('Y-m-d');
    }

    /**
     * Filter transfers, by selecting just current month's
     *
     * @param $transfers
     * @return mixed
     */
    protected function filterTransfers($transfers)
    {
        return $transfers->thisMonth($this->lastDate())
            ->selectRaw('DATE(transfer_date) as day, sum(amount) as daily_amount')
            ->groupBy('day');
    }

    /**
     * Generate each as label
     *
     * @return array
     */
    protected function createAxisData(): array
    {
        return ChartDataHandler::from([])->daysOfMonth()->get();
    }
}
