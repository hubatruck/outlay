<?php

namespace App\Charts;

use App\Models\TransactionType;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use ArielMejiaDev\LarapexCharts\PolarAreaChart;
use Arr;

class MonthlyTransactionByType extends MonthlyBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(string $walletID): PolarAreaChart
    {
        /// https://stackoverflow.com/a/24888904
        /// https://laravelquestions.com/2021/06/27/how-to-get-sum-and-count-date-with-groupby-in-laravel/
        $baseQuery = $this->getBaseQuery($walletID)
            ->selectRaw('transaction_type_id as type, sum(amount) as amount')
            ->groupBy('type');

        return $this->chart->polarAreaChart()
            ->setTitle(__('Transaction amounts by type'))
            ->addData($this->reduceDataPrecision($baseQuery->pluck('amount')->toArray()))
            ->setLabels($this->translateLabels(TransactionType::all()->pluck('name')->toArray()))
            ->setColors(Arr::shuffle(self::$colors));
    }
}