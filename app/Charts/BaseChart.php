<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Transaction;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BaseChart
{
    /**
     * Colors used for the chart
     *
     * @var string[]
     */
    public static array $colors = [
        '#008FFB',
        '#00E396',
        '#feb019',
        '#ff455f',
        '#775dd0',
        '#80effe',
        '#0077B5',
        '#ff6384',
        '#c9cbcf',
        '#0057ff',
        '#00a9f4',
        '#2ccdc9',
        '#5e72e4',
    ];

    /**
     * The date interval/range for the data
     */
    protected CarbonPeriod $range;

    /**
     * Chart component
     */
    protected LarapexChart $chart;

    public function __construct(LarapexChart $chart, CarbonPeriod $range)
    {
        $this->chart = $chart;
        $this->range = $range;
    }

    /**
     * Generate a base query that can be used for charts
     *
     * @return Builder<Transaction>
     */
    protected function getTransactionBaseQuery(string $walletID): Builder
    {
        return Transaction::with(['transactionType', 'wallet'])
            ->betweenDateRange($this->range)
            ->where('wallet_id', $walletID)
            ->whereHas(
                'wallet',
                fn ($q) => $q->where('user_id', Auth::user()->id ?? -1)
            );

    }

    /**
     * Filter transfers, by selecting just current month's
     */
    protected function filterTransfers(Builder|Transaction $transfers): mixed
    {
        return $transfers->betweenDateRange($this->range)
            ->selectRaw('DATE(transfer_date) as day, sum(amount) / 100 as daily_amount')
            ->groupBy('day');
    }

    /**
     * Generate each as label
     */
    protected function createAxisData(): array
    {
        return ChartDataHandler::from([], $this->range)->fillWithDaysOfRange()->get();
    }
}
