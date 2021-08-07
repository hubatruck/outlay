<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonthlyChartBase
{
    protected LarapexChart $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    /**
     * Generate a base query that can be used for charts
     *
     * @param string $walletID
     * @return Builder
     */
    protected function getBaseQuery(string $walletID): Builder
    {
        return Auth::user()->transactions()->with(['transactionType', 'sourceWallet', 'destinationWallet'])
//            ->join('wallets as src_wallet', 'source_wallet_id', '=', 'src_wallet.id')
//            ->join('wallets as dest_wallet', 'destination_wallet_id', '=', 'dest_wallet.id')
            ->join(
                'transaction_types',
                'transaction_type_id',
                '=',
                'transaction_types.id'
            )
//            ->whereIn('wallet_id', function ($query) {
//                /// https://stackoverflow.com/a/16815955
//                $query->select('id')->from('wallets')
//                    ->where('user_id', '=', Auth::user()->id ?? '-1');
//            })

//            ->orWhereHas('destinationWallet', function ($q) use ($walletID) {
//                $q->where('destination_wallet_id', '=', $walletID);
//            })
//            ->orWhereHas('sourceWallet', function ($q) use ($walletID) {
//                $q->where('source_wallet_id', '=', $walletID);
//            })
            ->whereDate('transaction_date', '>=', date('Y-m-01'))
            ->whereDate('transaction_date', '<=', $this->lastDate());
    }

    /**
     * Which day should be the last displayed for current month
     *
     * @return string
     */
    protected function lastDate(): string
    {
        return date('Y-m-d');
    }
}
