<?php

namespace App\Charts;

use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use ArielMejiaDev\LarapexCharts\PieChart;

class MonthlyTransferByType extends MonthlyChartBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(Wallet $wallet): PieChart
    {
        $transferIn = $this->filterTransfers($wallet->incomingTransfers())
            ->pluck('daily_amount')->sum();
        $transferOut = $this->filterTransfers($wallet->outgoingTransfers())
            ->pluck('daily_amount')->sum();
        return $this->chart->pieChart()
            ->setTitle(__('Transferred amounts by type'))
            ->addData($this->reduceDataPrecision([$transferIn, $transferOut]))
            ->setLabels($this->translateLabels(['Incoming transfer', 'Outgoing transfer']))
            ->setDataLabels();
    }
}
