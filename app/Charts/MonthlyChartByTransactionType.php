<?php

namespace App\Charts;

use App\Models\TransactionType;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use ArielMejiaDev\LarapexCharts\PolarAreaChart;

class MonthlyChartByTransactionType extends MonthlyChartBase
{
    protected LarapexChart $chart;

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
            ->setLabels($this->translateLabels(TransactionType::all()->pluck('name')->toArray()));
    }

    /**
     * Reduce precision of data to 2 decimals
     * @param array $data
     * @return array
     */
    private function reduceDataPrecision(array $data): array
    {
        $arr = array_map(static function ($item) {
            return floor($item * 100) / 100;
        }, $data);
        return array_sum($arr) ? $arr : [];
    }

    /**
     * Translate each label displayed by the chart
     *
     * @param $labels
     * @return array
     */
    private function translateLabels($labels): array
    {
        return array_map(static function ($item) {
            return __($item);
        }, $labels);
    }
}
