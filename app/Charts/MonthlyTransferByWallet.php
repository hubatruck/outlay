<?php

namespace App\Charts;

use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\HorizontalBar;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class MonthlyTransferByWallet extends MonthlyBase
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(Wallet $wallet): HorizontalBar
    {
        $out = $wallet->outgoingTransfers()
            ->with(['toWallet', 'toWallet.user'])
            ->without(['transactionType'])
            ->whereDate('transfer_date', '>=', date('Y-m-01'))
            ->whereDate('transfer_date', '<=', $this->lastDate())
            ->selectRaw('to_wallet_id, sum(amount) as amount')
            ->groupBy('to_wallet_id')
            ->get();
        $in = $wallet->incomingTransfers()
            ->with(['fromWallet', 'fromWallet.user'])
            ->without(['transactionType'])
            ->whereDate('transfer_date', '>=', date('Y-m-01'))
            ->whereDate('transfer_date', '<=', $this->lastDate())
            ->selectRaw('from_wallet_id, sum(amount) as amount')
            ->groupBy('from_wallet_id')
            ->get();
        $data = $this->mapDataToWallets($in, $out);

        return $this->chart->horizontalBarChart()
            ->setTitle(__('Transfers by wallet'))
            ->addData(
                __('Received'),
                $this->reduceDataPrecision(Arr::pluck($data, 'in'))
            )
            ->addData(
                __('Sent'),
                $this->reduceDataPrecision(Arr::pluck($data, 'out'))
            )
            ->setXAxis(array_keys($data))
            ->setColors(Arr::shuffle(self::$colors));
    }

    /**
     * Map incoming and outgoing transfers to wallet names
     *
     * @param Collection $incoming
     * @param Collection $outgoing
     * @return array
     */
    private function mapDataToWallets(Collection $incoming, Collection $outgoing): array
    {
        $data = [];
        foreach ($incoming as $in) {
            $key = $this->formatWalletName($in->fromWallet);
            $data[$key['name']] = [
                'in' => $in->amount,
                'out' => 0.00,
            ];
        }

        foreach ($outgoing as $out) {
            $key = $this->formatWalletName($out->toWallet);
            if (array_key_exists($key['name'], $data)) {
                $data[$key['name']]['out'] = $out->amount;
            } else {
                $data[$key['name']] = [
                    'out' => $out->amount,
                    'in' => 0.00,
                ];
            }
        }
        return $data;
    }

    /**
     * Append owner's name next to wallets not belonging to the user
     *
     * @param Wallet $wallet
     * @return array
     */
    private function formatWalletName(Wallet $wallet): array
    {
        if (Auth::user()->owns($wallet)) {
            return ['name' => $wallet->name];
        }
        return [
            'name' => $wallet->name . ' (' . $wallet->user->name . ')',
        ];
    }
}
