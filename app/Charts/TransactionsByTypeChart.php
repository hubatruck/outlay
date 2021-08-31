<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\TransactionType;
use ArielMejiaDev\LarapexCharts\PolarAreaChart;
use Arr;

class TransactionsByTypeChart extends BaseChart
{
    public function build(string $walletID): PolarAreaChart
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getBaseQuery($walletID)
            ->selectRaw('transaction_type_id as type, sum(amount) as amount')
            ->groupBy('type');

        $data = ChartDataHandler::from($baseQuery->pluck('amount'));
        $labels = ChartDataHandler::from(TransactionType::all()->pluck('name'))
            ->translate();

        return $this->chart->polarAreaChart()
            ->setTitle(__('Transaction amounts by type'))
            ->addData($data->reduceDPAndGet())
            ->setLabels($labels->get())
            ->setColors(Arr::shuffle(self::$colors));
    }
}
