<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\TransactionType;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LineChart;
use Illuminate\Support\Arr;

class DailyTransactionsChart extends BaseChart
{
    public function build(Wallet $wallet): LineChart
    {
        // https://stackoverflow.com/a/24888904
        // https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getTransactionBaseQuery($wallet->id)
            ->selectRaw('
                DATE(transaction_date) AS day,
                SUM(CASE WHEN transaction_type_id = ? THEN amount ELSE 0 END) / 100 AS income,
                SUM(CASE WHEN transaction_type_id = ? THEN amount ELSE 0 END) / 100 AS expense',
                [
                    TransactionType::INCOME,
                    TransactionType::EXPENSE,
                ]
            )
            ->groupBy('day');

        $rows = $baseQuery->get();

        $incomeMap = $rows->pluck('income', 'day');
        $expenseMap = $rows->pluck('expense', 'day');

        $income = ChartDataHandler::from($incomeMap)->setRange($this->range);
        $expense = ChartDataHandler::from($expenseMap)->setRange($this->range);

        return $this->chart->lineChart()
            ->setTitle(__('Daily transactions'))
            ->addData(
                $this->getData($income),
                __('Income'),
            )
            ->addData(
                $this->getData($expense),
                __('Expense')
            )
            ->setXAxis($this->createAxisData(), 'datetime')
            ->setGrid(false)
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
    }

    /**
     * Small function to not repeat transformation method calls on data sources.
     */
    private function getData(ChartDataHandler $cdh): array
    {
        return $cdh->addMissingDays()->get();
    }
}
