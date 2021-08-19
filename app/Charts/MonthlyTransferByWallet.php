<?php

namespace App\Charts;

use App\DataHandlers\ByWalletDataHandler;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\HorizontalBar;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;

class MonthlyTransferByWallet extends MonthlyBase
{
    protected LarapexChart $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(Wallet $wallet): HorizontalBar
    {
        $out = $wallet->outgoingTransfers()
            ->with(['toWallet', 'toWallet.user'])
            ->without(['transactionType'])
            ->thisMonth()
            ->selectRaw('to_wallet_id, sum(amount) as amount')
            ->groupBy('to_wallet_id')
            ->get();
        $in = $wallet->incomingTransfers()
            ->with(['fromWallet', 'fromWallet.user'])
            ->thisMonth()
            ->without(['transactionType'])
            ->selectRaw('from_wallet_id, sum(amount) as amount')
            ->groupBy('from_wallet_id')
            ->get();
        $data = ByWalletDataHandler::mapTransfersToWallets($in, $out);

        return $this->chart->horizontalBarChart()
            ->setTitle(__('Transfers by wallet'))
            ->addData(
                __('Received'),
                Arr::pluck($data->reduceDPAndGet(), 'in')
            )
            ->addData(
                __('Sent'),
                Arr::pluck($data->reduceDPAndGet(), 'out')
            )
            ->setXAxis($data->keys())
            ->setColors(Arr::shuffle(self::$colors));
    }
}
