<?php

namespace App\Charts;

use App\Models\Transaction;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonthlyBase
{
    /**
     * Colors used for the chart
     *
     * @var string[]
     */
    public static $colors = [
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
                    ->where('user_id', '=', Auth::user()->id ?? '-1');
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
     * Fill data with days that are not present in database
     *
     * @param array $data
     * @return array
     */
    protected function addEmptyDays(array $data): array
    {
        return $this->fillFromStartOfMonth(function ($date) use ($data) {
            return floor(($data[$date->format('Y-m-d')] ?? 0) * 100) / 100;
        });
    }

    /**
     * Fill array with each day of the month until today, using custom data
     * see https://stackoverflow.com/a/50854594
     *
     * @param callable $callback Function to work with each day's date
     * @return array
     */
    protected function fillFromStartOfMonth(callable $callback): array
    {
        $data = [];
        $period = CarbonPeriod::create(date('Y-m-01'), $this->lastDate());
        foreach ($period as $date) {
            $data[] = $callback($date);
        }
        return $data;
    }

    /**
     * Generate each as label
     *
     * @return array
     */
    protected function createAxisData(): array
    {
        return $this->fillFromStartOfMonth(function ($date) {
            return $date->format('Y-m-d');
        });
    }

    /**
     * Translate each label displayed by the chart
     *
     * @param $labels
     * @return array
     */
    protected function translateLabels($labels): array
    {
        return array_map(static function ($item) {
            return __($item);
        }, $labels);
    }

    /**
     * Reduce precision of data to 2 decimals
     * @param array $data
     * @return array
     */
    protected function reduceDataPrecision(array $data): array
    {
        $arr = array_map(static function ($item) {
            return floor($item * 100) / 100;
        }, $data);
        return array_sum($arr) ? $arr : [];
    }
}
