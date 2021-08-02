<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MonthlyChartByDay extends MonthlyChartBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(string $walletID)
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getBaseQuery($walletID)
            ->selectRaw('DATE(transaction_date) as day, sum(amount) as daily_amount')->groupBy('day');

        $income = $this->getForTransactionTypeOf($baseQuery, 1);
        $expense = $this->getForTransactionTypeOf($baseQuery, 2);
        return $this->chart->areaChart()
            ->setTitle(__('Daily transactions in :month', ['month' => __(date('F'))]))
            ->addData(__('Income'), $this->addEmptyDays($income->pluck('daily_amount', 'day')->toArray()))
            ->addData(__('Expense'), $this->addEmptyDays($expense->pluck('daily_amount', 'day')->toArray()))
            ->setXAxis($this->createAxisData());
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
        return (clone $baseQuery)->where('transaction_type_id', '=', $transactionType)->get();
    }

    /**
     * Fill data with days that are not present in database
     *
     * @param array $data
     * @return array
     */
    private function addEmptyDays(array $data): array
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
    private function fillFromStartOfMonth(callable $callback): array
    {
        $data = array();
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
    private function createAxisData(): array
    {
        return $this->fillFromStartOfMonth(function ($date) {
            return $date->format('Y-m-d');
        });
    }
}
