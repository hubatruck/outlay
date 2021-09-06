<?php

namespace App\Models;

use App\Feedbacks\WalletFeedback;
use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class Wallet
 *
 * @package App\Models
 * @property string|int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $notes
 * @property float $balance
 * @property int $is_public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read float $current_balance
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read Collection|Transfer[] $transfers
 * @property-read int|null $transfers_count
 * @property-read Collection|Transfer[] $incomingTransfers
 * @property-read int|null $incoming_transfers_count
 * @property-read Collection|Transfer[] $outgoingTransfers
 * @property-read int|null $outgoing_transfers_count
 * @property-read User $user
 * @method static WalletFactory factory(...$parameters)
 * @method static Builder|Wallet newModelQuery()
 * @method static Builder|Wallet newQuery()
 * @method static \Illuminate\Database\Query\Builder|Wallet onlyTrashed()
 * @method static Builder|Wallet query()
 * @method static Builder|Wallet whereBalance($value)
 * @method static Builder|Wallet whereCreatedAt($value)
 * @method static Builder|Wallet whereDeletedAt($value)
 * @method static Builder|Wallet whereId($value)
 * @method static Builder|Wallet whereIsPublic($value)
 * @method static Builder|Wallet whereName($value)
 * @method static Builder|Wallet whereNotes($value)
 * @method static Builder|Wallet whereUpdatedAt($value)
 * @method static Builder|Wallet whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Wallet withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Wallet withoutTrashed()
 * @mixin Model
 */
class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Fields that are fillable for the model
     * @var string
     */
    protected $fillable = ['user_id', 'name', 'balance', 'notes', 'is_public'];

    /**
     * Check if the provided wallet is wallet or if the user owns the wallet
     *
     * @param Wallet|null $wallet
     * @return RedirectResponse|null
     */
    public static function check(Wallet $wallet = null): ?RedirectResponse
    {
        if ($wallet === null) {
            return WalletFeedback::existError();
        }
        if (!Auth::user()->owns($wallet)) {
            return WalletFeedback::editError();
        }
        return null;
    }

    /**
     * Get every public wallet + all the wallets belonging to the user
     *
     * @return Collection
     */
    public static function everyPublicWallet(): Collection
    {
        return Auth::user()->wallets()
            ->orWhere('is_public', '=', 1)
            ->get();
    }

    /**
     * Get the user who owns this wallet
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specified wallet has transactions
     *
     * @return bool
     */
    public function hasTransactions(): bool
    {
        return $this->transactions()->exists();
    }

    /**
     * Transactions belonging to this wallet
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Transfers made to this wallet
     *
     * @return HasMany
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_wallet_id');
    }

    /**
     * Check if the wallet has any related transfer
     *
     * @return bool
     */
    public function hasTransfers(): bool
    {
        return $this->transfers()->exists();
    }

    /**
     * All transfers related to a wallet
     *
     * @return HasMany
     */
    public function transfers(): HasMany
    {
        $relation = $this->hasMany(Transfer::class);
        $relation->setQuery(
            Transfer::where(function ($query) {
                $query->where('from_wallet_id', '=', $this->id)
                    ->orWhere('to_wallet_id', '=', $this->id);
            })->getQuery()
        );
        return $relation;
    }

    /**
     * Get wallet balance as of today
     *
     * @return float
     */
    public function getCurrentBalanceAttribute(): float
    {
        return $this->getBalanceBetween(null, Carbon::now());
    }

    /**
     * Get balance of wallet taking int account transactions and transfers
     * in a specified date interval
     *
     * @param \Carbon\Carbon|string|null $from
     * @param \Carbon\Carbon|string|null $to
     * @return float
     */
    public function getBalanceBetween(\Carbon\Carbon|string $from = null, \Carbon\Carbon|string $to = null): float
    {
        $transactionVal = $this->transactions()
            ->sumAmount();

        $transfersVal = $this->transfers()
            ->sumAmount($this->id);

        if ($from !== null) {
            $transactionVal->whereDate('transaction_date', '>=', $from);
            $transfersVal->whereDate('transfer_date', '>=', $from);
        }
        if ($to !== null) {
            $transactionVal->whereDate('transaction_date', '<=', $to);
            $transfersVal->whereDate('transfer_date', '<=', $to);
        }

        $transactionVal = $transactionVal->first()
                ->getAttributeValue('amount') ?? 0.0;
        $transfersVal = $transfersVal->first()
                ->getAttributeValue('amount') ?? 0.0;
        return reducePrecision($transactionVal + $transfersVal);
    }

    /**
     * Transfers made from this wallet
     *
     * @return HasMany
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_wallet_id');
    }
}
