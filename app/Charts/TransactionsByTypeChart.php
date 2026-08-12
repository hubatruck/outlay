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
        $amountByType = $this->getTransactionBaseQuery($wallet->id)
            ->selectRaw('transaction_type_id, SUM(amount) as amount')
            ->whereIn('transaction_type_id', [TransactionType::INCOME, TransactionType::EXPENSE])
            ->groupBy('transaction_type_id')
            ->pluck('amount', 'transaction_type_id');

        $data = [
            (float) ($amountByType[TransactionType::INCOME] ?? 0),
            (float) ($amountByType[TransactionType::EXPENSE] ?? 0),
        ];

        $labels = ChartDataHandler::from(
            TransactionType::all()->pluck('name')
        )->translate();


        return $this->chart->polarAreaChart()
            ->setTitle(__('Transaction amounts by type'))
            ->addData($data)
            ->setLabels($labels->get())
            ->setColors(Arr::shuffle(self::$colors));
    }
}
