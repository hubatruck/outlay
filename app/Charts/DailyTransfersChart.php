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
        $rows = $wallet->transfers()
            ->betweenDateRange($this->range)
            ->selectRaw('
                DATE(transfer_date) as day,
                SUM(CASE WHEN to_wallet_id = ?   THEN amount ELSE 0 END) / 100 as incoming_amount,
                SUM(CASE WHEN from_wallet_id = ? THEN amount ELSE 0 END) / 100 as outgoing_amount
            ', [$wallet->id, $wallet->id])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $incomingMap = $rows->pluck('incoming_amount', 'day');
        $outgoingMap = $rows->pluck('outgoing_amount', 'day');

        $transferIn = ChartDataHandler::from($incomingMap, $this->range);
        $transferOut = ChartDataHandler::from($outgoingMap, $this->range);

        return $this->chart->barChart()
            ->setTitle(__('Daily transfers'))
            ->addData(
                $this->getData($transferIn),
                __('Incoming transfer'),
            )
            ->addData(
                $this->getData($transferOut),
                __('Outgoing transfer')
            )
            ->setXAxis($this->createAxisData(), 'datetime')
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
    }

    /**
     * Small function to not repeat transformation method calls on data handlers
     */
    private function getData(ChartDataHandler $cdh): array
    {
        return $cdh->addMissingDays()->get();
    }
}
