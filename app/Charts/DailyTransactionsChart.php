<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\TransactionType;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LineChart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class DailyTransactionsChart extends BaseChart
{
    public function build(Wallet $wallet): LineChart
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getTransactionBaseQuery($wallet->id)
            ->selectRaw('DATE(transaction_date) as day, sum(amount) as daily_amount')
            ->groupBy('day');

        $income = ChartDataHandler::from($this->getForTransactionTypeOf($baseQuery, TransactionType::INCOME)->pluck('daily_amount', 'day'))
            ->setRange($this->range);
        $expense = ChartDataHandler::from($this->getForTransactionTypeOf($baseQuery, TransactionType::EXPENSE)->pluck('daily_amount', 'day'))
            ->setRange($this->range);

        return $this->chart->lineChart()
            ->setTitle(__('Daily transactions'))
            ->addData(
                __('Income'),
                $this->getData($income)
            )
            ->addData(
                __('Expense'),
                $this->getData($expense)
            )
            ->setXAxis($this->createAxisData())
            ->setGrid(false)
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
    }

    /**
     * Filter transactions by specified transaction type ID
     *
     * @param Builder $baseQuery
     * @param int $transactionType
     * @return Builder[]|Collection
     */
    private function getForTransactionTypeOf(Builder $baseQuery, int $transactionType): Collection|array
    {
        /// https://stackoverflow.com/a/46227628 (comment)
        return (clone $baseQuery)
            ->where('transaction_type_id', '=', $transactionType)
            ->get();
    }

    /**
     * Small function to not repeat transformation method calls on data sources.
     *
     * @param ChartDataHandler $cdh
     * @return array
     */
    private function getData(ChartDataHandler $cdh): array
    {
        return $cdh->addMissingDays()->reduceDPAndGet();
    }
}
