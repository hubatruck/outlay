<?php

namespace App\Charts;

use App\DataHandlers\ChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\PieChart;
use Illuminate\Support\Arr;

class TransfersByTypeChart extends BaseChart
{
    public function build(Wallet $wallet): PieChart
    {
        $data = $this->getData($wallet);

        return $this->chart->pieChart()
            ->setTitle(__('Transferred amounts by type'))
            ->addData($data->get())
            ->setLabels(ChartDataHandler::from(['Incoming transfer', 'Outgoing transfer'])->translate()->get())
            ->setDataLabels()
            ->setColors(Arr::shuffle(self::$colors));
    }

    private function getData(Wallet $wallet): ChartDataHandler
    {
        $transferIn = $this->filterTransfers($wallet->incomingTransfers())
            ->pluck('daily_amount')
            ->sum();

        $transferOut = $this->filterTransfers($wallet->outgoingTransfers())
            ->pluck('daily_amount')
            ->sum();

        $data = ($transferIn > 0 || $transferOut > 0)
            ? [$transferIn, $transferOut]
            : [];

        return ChartDataHandler::from($data);
    }
}
