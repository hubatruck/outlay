<?php

namespace App\Charts;

use App\DataHandlers\BalanceChartDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\AreaChart;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;
use Carbon\Carbon;

class MonthlyBalanceByDay extends MonthlyBase
{
    protected $chart;
    protected $wallet;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

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
            ->setColors(Arr::shuffle(self::$colors));
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
                ->sumAmount()
                ->selectRaw('DATE(transaction_date) as day')
                ->where('transaction_date', '<=', $this->lastDate())
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day')
        )->addMissingDays();

        /**
         * @var BalanceChartDataHandler $transferBalance
         */
        $transferBalance = BalanceChartDataHandler::from(
            $this->wallet->transfers()
                ->sumAmount($this->wallet->id)
                ->selectRaw('DATE(transfer_date) as day')
                ->where('transfer_date', '<=', $this->lastDate())
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day')
        )->addMissingDays();

        return $transferBalance->with($transactionBalance)
            ->sumWithPreviousDays()
            ->offsetBalance($this->wallet->getBalanceBetween(null, Carbon::now()))
            ->reduceDPAndGet();
    }
}
