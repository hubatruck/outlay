<?php

namespace App\Charts;

use App\DataHandlers\BalanceChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\AreaChart;
use Arr;

class DailyBalanceChart extends BaseChart
{
    /**
     * Current wallet
     * @var Wallet
     */
    protected Wallet $wallet;

    public function build(Wallet $wallet): AreaChart
    {
        $this->wallet = $wallet;
        return $this->chart->AreaChart()
            ->setTitle(__('Daily balance'))
            ->addData(
                __('Balance'),
                $this->getData()
            )
            ->setXAxis($this->createAxisData())
            ->setGrid(false)
            ->setColors(Arr::shuffle(self::$colors))
            ->setToolbar(true);
    }

    /**
     * Get the data for the chart
     *
     * @return array
     */
    private function getData(): array
    {
        /**
         * @var BalanceChartDataHandler $transactionBalance
         */
        $transactionBalance = BalanceChartDataHandler::from(
            $this->wallet->transactions()
                ->betweenDateRange($this->range)
                ->sumAmount()
                ->selectRaw('DATE(transaction_date) as day')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day'),
            $this->range
        )->addMissingDays();

        /**
         * @var BalanceChartDataHandler $transferBalance
         */
        $transferBalance = BalanceChartDataHandler::from(
            $this->wallet->transfers()
                ->sumAmount($this->wallet->id)
                ->betweenDateRange($this->range)
                ->selectRaw('DATE(transfer_date) as day')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day'),
            $this->range
        )->addMissingDays();

        return $transferBalance->with($transactionBalance)
            ->sumWithPreviousDays()
            ->offsetBalance($this->wallet->getBalanceBetween(null, $this->range->last()))
            ->reduceDPAndGet();
    }
}
