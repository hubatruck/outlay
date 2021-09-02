<?php

namespace App\DataHandlers;

use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class BalanceChartDataHandler extends ChartDataHandler
{
    /**
     * Creates a new instance of this class
     *
     * @param array|Collection|null $data
     * @param CarbonPeriod|null $range
     * @return BalanceChartDataHandler
     */
    protected static function newInstance(array|Collection $data = null, CarbonPeriod $range = null): BalanceChartDataHandler
    {
        return new BalanceChartDataHandler($data, $range);
    }

    /**
     * Join instance with another one
     * Note: Data keys should be the same.
     *
     * @param BalanceChartDataHandler $other
     * @return $this
     */
    public function with(BalanceChartDataHandler $other): BalanceChartDataHandler
    {
        foreach ($this->data as $day => $amount) {
            $this->data[$day] += $other->data[$day];
        }
        return $this;
    }

    /**
     * Offset the balance data, so it will be displayed correctly
     * This is needed, because otherwise previous month's balances won't
     * be taken into account.
     *
     * @param float $walletBalance Balance of the wallet, as of today
     * @return $this
     */
    public function offsetBalance(float $walletBalance): BalanceChartDataHandler
    {
        $offset = reducePrecision($walletBalance) - reducePrecision($this->data[array_key_last($this->data)]);

        foreach ($this->data as &$item) {
            $item += $offset;
        }
        return $this;
    }

    /**
     * Instead of showing a per day balance, make the balance continuous.
     *
     * @return $this
     */
    public function sumWithPreviousDays(): BalanceChartDataHandler
    {
        $keys = array_keys($this->data);
        for ($i = 1; $i < sizeof($this->data); $i++) {
            $this->data[$keys[$i]] += $this->data[$keys[$i - 1]];
        }
        return $this;
    }
}
