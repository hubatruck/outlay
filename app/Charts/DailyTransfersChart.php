<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\BarChart;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;
use Carbon\CarbonPeriod;

class DailyTransfersChart extends BaseChart
{
    protected LarapexChart $chart;

    public function __construct(LarapexChart $chart, CarbonPeriod $range)
    {
        $this->chart = $chart;
        $this->range = $range;
    }

    public function build(Wallet $wallet): BarChart
    {
        $transferIn = ChartDataHandler::from(
            $this->filterTransfers($wallet->incomingTransfers())->pluck('daily_amount', 'day'),
            $this->range
        );
        $transferOut = ChartDataHandler::from(
            $this->filterTransfers($wallet->outgoingTransfers())->pluck('daily_amount', 'day'),
            $this->range
        );

        return $this->chart->barChart()
            ->setTitle(__('Daily transfers'))
            ->addData(
                __('Incoming transfer'),
                $this->getData($transferIn)
            )
            ->addData(
                __('Outgoing transfer'),
                $this->getData($transferOut)
            )
            ->setXAxis($this->createAxisData())
            ->setColors(Arr::shuffle(self::$colors));
    }

    /**
     * Small function to not repeat transformation method calls on data handlers
     *
     * @param ChartDataHandler $cdh
     * @return array
     */
    private function getData(ChartDataHandler $cdh): array
    {
        return $cdh->addMissingDays()->reduceDPAndGet();
    }
}
