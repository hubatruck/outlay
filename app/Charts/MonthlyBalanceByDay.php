<?php

namespace App\Charts;

use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\AreaChart;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;

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
        $transactionBalance = $this->addEmptyDays(
            $this->wallet->transactions()
                ->sumAmount()
                ->selectRaw('DATE(transaction_date) as day')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('amount', 'day')
                ->toArray()
        );

        $transferBalance = $this->addEmptyDays(
            $this->wallet->transfers()
                ->sumAmount($this->wallet->id)
                ->selectRaw('DATE(transfer_date) as day')
                ->groupBy('day')
                ->pluck('amount', 'day')
                ->toArray()
        );
        return $this->reduceDataPrecision($this->offsetBalance($this->sumWithPreviousDays($this->joinData($transferBalance, $transactionBalance))));
    }

    /**
     * Offset balance for chart, so previous month's balance is taken into account when
     * displaying the data
     *
     * @param $data
     * @return array
     */
    private function offsetBalance($data): array
    {
        $offset = $this->wallet->currentBalance - $data[array_key_last($data)];

        foreach ($data as &$item) {
            $item += $offset;
        }
        return $data;
    }

    /**
     * Convert balance, so that every day represents the balance of the wallet
     * until that moment
     *
     * @param array $data
     * @return array
     */
    private function sumWithPreviousDays(array $data): array
    {
        $keys = array_keys($data);
        for ($i = 1; $i < sizeof($data); $i++) {
            $data[$keys[$i]] += $data[$keys[$i - 1]];
        }
        return $data;
    }

    /**
     * Join two array into a single one
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    private function joinData($array1, $array2): array
    {
        foreach ($array1 as $day => $amount) {
            $array1[$day] += $array2[$day];
        }
        return $array1;
    }
}
