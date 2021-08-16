<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Log;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read Collection|Transfer[] $transfers
 * @property-read int|null $transfers_count
 * @property-read Collection|Wallet[] $wallets
 * @property-read int|null $wallets_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Transactions belonging to the user
     *
     * @return HasManyThrough
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class)
            ->withTrashedParents();
    }

    /**
     * Get previous transaction date created by the user.
     * This function is used to determine the prefill date for transaction
     * creation.
     * @return string
     */
    public function previousTransactionDate(): string
    {
        $last_transaction = $this->transactions->last();
        return ($last_transaction !== null) ? (string) $last_transaction->transaction_date : (string) now();
    }

    /**
     * Check if user has transactions
     *
     * @return bool
     */
    public function hasTransactions(): bool
    {
        return $this->transactions->first() !== null;
    }

    /**
     * Check if user has any active wallet
     *
     * @return bool
     */
    public function hasAnyActiveWallet(): bool
    {
        return $this->activeWallets()->first() !== null;
    }

    /**
     * Get all active wallets for user
     * @return Collection
     */
    public function activeWallets(): Collection
    {
        return $this->wallets()->withoutTrashed()->get();
    }

    /**
     * Wallets belonging to the user
     *
     * @return HasMany
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class)->withTrashed();
    }

    /**
     * Check if user has wallet of any status
     *
     * @return bool
     */
    public function hasWallet(): bool
    {
        return $this->wallets->first() !== null;
    }

    /**
     * Check if user has transfers
     *
     * @return bool
     */
    public function hasTransfers(): bool
    {
        return $this->transfers()->first() !== null;
    }

    /**
     * Transfers made from or to any of the user's wallets
     *
     * return Builder
     */
    public function transfers()
    {
        return Transfer::select(['transfers.*', 'wallets_to.name as to_wallet_name', 'wallets_from.name as from_wallet_name'])
            ->join('wallets as wallets_from', 'wallets_from.id', '=', 'from_wallet_id')
            ->leftJoin('wallets as wallets_to', 'wallets_to.id', '=', 'to_wallet_id')
            ->whereRaw('(wallets_to.user_id = ? or wallets_from.user_id = ?)', [$this->id, $this->id]);
    }

    /**
     * Check if the user owns a transaction or wallet
     *
     * @param Transaction|Wallet|null $item
     * @return bool
     */
    public function owns($item): bool
    {
        if ($item instanceof Transaction) {
            $item = $item->wallet;
        }
        if ($item instanceof Wallet) {
            return (string) $this->id === (string) $item->user_id;
        }

        Log::warning('Invalid argument type for ownership check.');
        return false;
    }
}
