<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use ArielMejiaDev\LarapexCharts\LineChart;
use Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MonthlyTransactionByDay extends MonthlyBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(string $walletID): LineChart
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getBaseQuery($walletID)
            ->selectRaw('DATE(transaction_date) as day, sum(amount) as daily_amount')
            ->groupBy('day');

        $income = $this->getForTransactionTypeOf($baseQuery, 1);
        $expense = $this->getForTransactionTypeOf($baseQuery, 2);
        return $this->chart->lineChart()
            ->setTitle(__('Daily transactions'))
            ->addData(
                __('Income'),
                $this->addEmptyDays($income->pluck('daily_amount', 'day')->toArray())
            )
            ->addData(
                __('Expense'),
                $this->addEmptyDays($expense->pluck('daily_amount', 'day')->toArray())
            )
            ->setXAxis($this->createAxisData())
            ->setGrid(false)
            ->setColors(Arr::shuffle(self::$colors));
    }

    /**
     * Filter transactions by specified transaction type ID
     *
     * @param Builder $baseQuery
     * @param int $transactionType
     * @return Builder[]|Collection
     */
    private function getForTransactionTypeOf(Builder $baseQuery, int $transactionType)
    {
        /// https://stackoverflow.com/a/46227628 (comment)
        return (clone $baseQuery)
            ->where('transaction_type_id', '=', $transactionType)
            ->get();
    }
}
