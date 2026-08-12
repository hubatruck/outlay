<?php

namespace App\Charts;

use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\HorizontalBar;
use Illuminate\Support\Arr;

class TransfersByWalletChart extends BaseChart
{
    public function build(Wallet $wallet): HorizontalBar
    {
        $start = $this->range->first()->startOfDay();
        $endExclusive = $this->range->last()->copy()->addDay()->startOfDay();

        $received = (float) ($wallet->incomingTransfers()
            ->without(['transactionType'])
            ->whereBetween('transfer_date', [$start, $endExclusive])
            ->selectRaw('SUM(amount) AS amount')
            ->value('amount') ?? 0);

        $sent = (float) ($wallet->outgoingTransfers()
            ->without(['transactionType'])
            ->whereBetween('transfer_date', [$start, $endExclusive])
            ->selectRaw('SUM(amount) AS amount')
            ->value('amount') ?? 0);

        return $this->chart->horizontalBarChart()
            ->setTitle(__('Transfers by wallet'))
            ->addData([$received], __('Received'))
            ->addData([$sent], __('Sent'))
            ->setXAxis([__('Total')])
            ->setColors(Arr::shuffle(self::$colors));
    }
}
