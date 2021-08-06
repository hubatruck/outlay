<?php

namespace App\Models;

use App\Feedbacks\TransactionFeedback;
use Carbon\Carbon;
use Database\Factories\TransactionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Transaction
 *
 * @property string|int $id
 * @property string|int $source_wallet_id
 * @property string|int $destination_wallet_id
 * @property float $amount
 * @property string|null $scope
 * @property int $transaction_type_id
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read string $type
 * @property-read string $source_wallet_name
 * @property-read string $destination_wallet_name
 * @property-read TransactionType $transactionType
 * @property-read Wallet|null $sourceWallet
 * @property-read Wallet|null $destinationWallet
 * @method static TransactionFactory factory(...$parameters)
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereDeletedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereScope($value)
 * @method static Builder|Transaction whereTransactionDate($value)
 * @method static Builder|Transaction whereTransactionTypeId($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereWalletId($value)
 * @mixin Eloquent
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

    protected $appends = [
        'source_wallet_name',
        'destination_wallet_name',
        'type',
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    protected $dates = [
        'transaction_date'
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
        if (
            $transaction === null
            || ($transaction->sourceWallet === null && $transaction->destinationWallet === null)
        ) {
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
     * Wallet from where the funds came from
     * @return BelongsTo
     */
    public function sourceWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'source_wallet_id', 'id')
            ->withTrashed();
    }

    /**
     * Wallet where the funds went to
     *
     * @return BelongsTo
     */
    public function destinationWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'destination_wallet_id', 'id')
            ->withTrashed();
    }

    /**
     * Append the wallet name
     * @return string
     */
    public function getSourceWalletNameAttribute(): string
    {
        return $this->walletNameFor($this->source_wallet_id);
    }

    /**
     * Get wallet name for a specified ID
     *
     * @param $wallet_id
     * @return string
     */
    private function walletNameFor($wallet_id): string
    {
        return Wallet::withTrashed()
                ->find($wallet_id)
                ->name ?? '-';
    }

    /**
     * Append the wallet name
     * @return string
     */
    public function getDestinationWalletNameAttribute(): string
    {
        return $this->walletNameFor($this->destination_wallet_id);
    }

    /**
     * Append transaction type
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return TransactionType::find($this->transaction_type_id)
            ->name;
    }

    /**
     * Set transaction type from DATE format to DATETIME format
     * @param Carbon|string $value
     */
    public function setTransactionDateAttribute($value): void
    {
        $this->attributes['transaction_date'] = (Carbon::parse($value)->format('Y-m-d') . ' 03:00:00');
    }
}
