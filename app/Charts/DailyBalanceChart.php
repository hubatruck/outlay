<?php

namespace App\Charts;

use App\DataHandlers\BalanceChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\AreaChart;
use Illuminate\Support\Arr;

class DailyBalanceChart extends BaseChart
{
    /**
     * Current wallet
     */
    protected Wallet $wallet;

    public function build(Wallet $wallet): AreaChart
    {
        $this->wallet = $wallet;

        return $this->chart->AreaChart()
            ->setTitle(__('Daily balance'))
            ->addData($this->getData(), __('Balance'))
            ->setXAxis($this->createAxisData(), 'datetime')
            ->setGrid(false)
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
    }

    private function dailySeriesForTransactions(Wallet $wallet): BalanceChartDataHandler
    {
        return BalanceChartDataHandler::from(
            $wallet->transactions()
                ->betweenDateRange($this->range)
                ->sumAmount()
                ->selectRaw('DATE(transaction_date) as day')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day'),
            $this->range
        )->addMissingDays();
    }

    private function dailySeriesForTransfers(Wallet $wallet): BalanceChartDataHandler
    {
        return BalanceChartDataHandler::from(
            $wallet->transfers()
                ->sumAmount($wallet->id)
                ->betweenDateRange($this->range)
                ->selectRaw('DATE(transfer_date) as day')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day'),
            $this->range
        )->addMissingDays();
    }

    private function getData(): array
    {
        $transactionBalance = $this->dailySeriesForTransactions($this->wallet);
        $transferBalance = $this->dailySeriesForTransfers($this->wallet);

        return $transferBalance
            ->with($transactionBalance)
            ->sumWithPreviousDays()
            ->offsetBalance($this->wallet->getBalanceBetween(null, $this->range->last()))
            ->get();
    }
}
