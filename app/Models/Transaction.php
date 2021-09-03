<?php

namespace App\Models;

use App\Feedbacks\TransactionFeedback;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $wallet_id
 * @property float $amount
 * @property string|null $scope
 * @property int $transaction_type_id
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read TransactionType $transactionType
 * @property-read Wallet $wallet
 * @method static TransactionFactory factory(...$parameters)
 * @method static Builder|Transaction betweenDateRange(CarbonPeriod $range)
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction sumAmount()
 * @method static Builder|Transaction thisMonth($lastDay = null)
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereDeletedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereScope($value)
 * @method static Builder|Transaction whereTransactionDate($value)
 * @method static Builder|Transaction whereTransactionTypeId($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereWalletId($value)
 * @mixin Model
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'scope',
        'transaction_type_id',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    protected $dates = [
        'transaction_date',
    ];

    /**
     * The function checks the following:
     * - Transaction is valid
     * - User owns the transaction
     *
     * If one of the check is failed, an appropriate error redirect is returned
     *
     * @param Transaction|null $transaction
     * @return RedirectResponse|null
     */
    public static function checkStatus(Transaction $transaction = null): ?RedirectResponse
    {
        if ($transaction === null || $transaction->wallet === null) {
            return TransactionFeedback::existError();
        }
        if (!Auth::user()->owns($transaction)) {
            return TransactionFeedback::editError();
        }
        return null;
    }

    /**
     * Type of the transaction
     * @return BelongsTo
     */
    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }

    /**
     * Wallet that the transaction belongs to
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class)
            ->withTrashed();
    }

    /**
     * Set transaction type from DATE format to DATETIME format
     * @param string|Carbon $value
     */
    public function setTransactionDateAttribute(Carbon|string $value): void
    {
        $this->attributes['transaction_date'] = (Carbon::parse($value)->format(globalDateFormat()) . ' 03:00:00');
    }

    /**
     * Only get transactions occurred this month
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
     * Return transactions occurred in a specific date range
     *
     * @param Builder $query
     * @param CarbonPeriod $range
     * @return Builder
     */
    public function scopeBetweenDateRange(Builder $query, CarbonPeriod $range): Builder
    {
        return $query->whereDate('transaction_date', '>=', $range->first())
            ->whereDate('transaction_date', '<=', $range->last());
    }

    /**
     * Sum transaction amount by transaction type
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSumAmount(Builder $query): Builder
    {
        return $query
            ->selectRaw(
                'sum(case when transaction_type_id = ? then amount when transaction_type_id = ? then -amount end) as amount',
                [TransactionType::INCOME, TransactionType::EXPENSE]
            );
    }
}
