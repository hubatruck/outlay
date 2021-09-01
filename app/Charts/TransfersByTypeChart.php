<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\PieChart;
use Arr;

class TransfersByTypeChart extends BaseChart
{
    public function build(Wallet $wallet): PieChart
    {
        $transferIn = $this->filterTransfers($wallet->incomingTransfers())
            ->pluck('daily_amount')->sum();
        $transferOut = $this->filterTransfers($wallet->outgoingTransfers())
            ->pluck('daily_amount')->sum();
        $data = ChartDataHandler::from([$transferIn, $transferOut]);

        return $this->chart->pieChart()
            ->setTitle(__('Transferred amounts by type'))
            ->addData($data->reduceDPAndGet())
            ->setLabels(ChartDataHandler::from(['Incoming transfer', 'Outgoing transfer'])->translate()->get())
            ->setDataLabels()
            ->setColors(Arr::shuffle(self::$colors));
    }
}