<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
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
        $transferIn = ChartDataHandler::from(
            $this->filterTransfers($wallet->incomingTransfers())->pluck('daily_amount', 'day')
        );
        $transferOut = ChartDataHandler::from(
            $this->filterTransfers($wallet->outgoingTransfers())->pluck('daily_amount', 'day')
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
