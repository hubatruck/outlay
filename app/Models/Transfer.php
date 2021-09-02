<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Transfer
 *
 * @property int $id
 * @property int $from_wallet_id
 * @property int $to_wallet_id
 * @property float $amount
 * @property string|null $description
 * @property Carbon $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Wallet|null $fromWallet
 * @property-read Wallet|null $toWallet
 * @method static TransferFactory factory(...$parameters)
 * @method static Builder|Transfer betweenDateRange(CarbonPeriod $range)
 * @method static Builder|Transfer newModelQuery()
 * @method static Builder|Transfer newQuery()
 * @method static Builder|Transfer query()
 * @method static Builder|Transfer sumAmount(string $walletID)
 * @method static Builder|Transfer thisMonth($lastDay = null)
 * @method static Builder|Transfer whereAmount($value)
 * @method static Builder|Transfer whereCreatedAt($value)
 * @method static Builder|Transfer whereDescription($value)
 * @method static Builder|Transfer whereFromWalletId($value)
 * @method static Builder|Transfer whereId($value)
 * @method static Builder|Transfer whereToWalletId($value)
 * @method static Builder|Transfer whereTransferDate($value)
 * @method static Builder|Transfer whereUpdatedAt($value)
 * @mixin Model
 */
class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'amount',
        'description',
        'transfer_date',
    ];

    protected $dates = [
        'transfer_date',
    ];

    /**
     * Destination wallet
     *
     * @return HasOne
     */
    public function toWallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'id', 'to_wallet_id')
            ->withTrashed();
    }

    /**
     * Source wallet
     *
     * @return HasOne
     */
    public function fromWallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'id', 'from_wallet_id')
            ->withTrashed();
    }

    /**
     * Set transfer date from DATE format to DATETIME format
     *
     * @param Carbon|string $value
     */
    public function setTransferDateAttribute(Carbon|string $value): void
    {
        $this->attributes['transfer_date'] = (Carbon::parse($value)->format(globalDateFormat()) . ' 03:00:00');
    }

    /**
     * Only get transfers occurred this month
     *
     * @param Builder $query
     * @param null $lastDay
     * @return Builder
     */
    public function scopeThisMonth(Builder $query, $lastDay = null): Builder
    {
        $lastDay = $lastDay ?? currentDayOfTheMonth();
        return $this->scopeBetweenDateRange($query, CarbonPeriod::create(date('Y-m-01'), $lastDay));
    }

    /**
     * Return transfers occurred in a specific date range
     *
     * @param Builder $query
     * @param CarbonPeriod $range
     * @return Builder
     */
    public function scopeBetweenDateRange(Builder $query, CarbonPeriod $range): Builder
    {
        return $query->whereDate('transfer_date', '>=', $range->first())
            ->whereDate('transfer_date', '<=', $range->last());
    }


    /**
     * Sum incoming and outgoing transaction amounts
     *
     * @param Builder $query
     * @param string $walletID
     * @return Builder
     */
    public function scopeSumAmount(Builder $query, string $walletID): Builder
    {
        return $query->
        selectRaw('sum(case when from_wallet_id = ? then -amount when to_wallet_id = ? then amount end) as amount', [$walletID, $walletID]);
    }
}
