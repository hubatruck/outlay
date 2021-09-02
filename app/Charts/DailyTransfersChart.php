<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\BarChart;
use Illuminate\Support\Arr;

class DailyTransfersChart extends BaseChart
{
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
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
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
