<?php

namespace App\Charts;

use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\BarChart;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;

class MonthlyTransferByDay extends MonthlyBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(Wallet $wallet): BarChart
    {
        $transferIn = $this->filterTransfers($wallet->incomingTransfers())->get();
        $transferOut = $this->filterTransfers($wallet->outgoingTransfers())->get();

        return $this->chart->barChart()
            ->setTitle(__('Daily transfers'))
            ->addData(
                __('Incoming transfer'),
                $this->addEmptyDays($transferIn->pluck('daily_amount', 'day')->toArray())
            )
            ->addData(
                __('Outgoing transfer'),
                $this->addEmptyDays($transferOut->pluck('daily_amount', 'day')->toArray())
            )
            ->setXAxis($this->createAxisData())
            ->setColors(Arr::shuffle(self::$colors));
    }
}
