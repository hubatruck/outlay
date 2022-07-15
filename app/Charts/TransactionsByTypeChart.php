<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\TransactionType;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\PolarAreaChart;
use Illuminate\Support\Arr;

class TransactionsByTypeChart extends BaseChart
{
    public function build(Wallet $wallet): PolarAreaChart
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getTransactionBaseQuery($wallet->id)
            ->selectRaw('transaction_type_id as type, sum(amount) as amount')
            ->groupBy('type')
            ->orderBy('type');

        /// Workaround in case when the wallet has got only expenses, but no income
        $data = $baseQuery->pluck('amount')->toArray();
        if (!empty($data) &&
            !$wallet->transactions()
                ->where('transaction_type_id', '=', TransactionType::INCOME)
                ->betweenDateRange($this->range)
                ->exists()
        ) {
            $data = [0, $data[0]];
        }
        $data = ChartDataHandler::from($data);

        $labels = ChartDataHandler::from(TransactionType::all()->pluck('name'))
            ->translate();

        return $this->chart->polarAreaChart()
            ->setTitle(__('Transaction amounts by type'))
            ->addData($data->get())
            ->setLabels($labels->get())
            ->setColors(Arr::shuffle(self::$colors));
    }
}
