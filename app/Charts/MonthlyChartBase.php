<?php


namespace App\Charts;


use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MonthlyChartBase
{
    protected function getBaseQuery(string $walletID): Builder
    {
        return Transaction::with(['transactionType', 'wallet'])
            ->join('wallets', 'wallet_id', '=', 'wallets.id')
            ->join('transaction_types', 'transaction_type_id', '=', 'transaction_types.id')
            ->whereIn('wallet_id', function ($query) {
                /// https://stackoverflow.com/a/16815955
                $query->select('id')->from('wallets')->where('user_id', '=', Auth::user()->id ?? '-1');
            })
            ->where('wallet_id', '=', $walletID)
            ->whereBetween('transaction_date', [date('Y-m-01'), $this->lastDate()]);
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
