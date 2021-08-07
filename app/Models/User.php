<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
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
     * Get previous transaction date created by the user.
     * This function is used to determine the prefill date for transaction
     * creation.
     * @return string
     */
    public function previousTransactionDate(): string
    {
        $last_transaction = $this->transactions()->latest()->first();
        return ($last_transaction !== null) ? (string) $last_transaction->transaction_date : (string) now();
    }

    /**
     * Transactions that belong to the user
     *
     * @return Builder
     */
    public function transactions(): Builder
    {
        $wallets = $this->wallets()->pluck('id')->toArray();
        $placeholder = implode(',', array_fill(0, count($wallets), '?'));
        if ($placeholder === '') { /// no wallets
            $placeholder = '?';
            $wallets = [-1];
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Transaction::whereRaw(
            '(wallet_id in (' . $placeholder . ') or destination_wallet_id in (' . $placeholder . '))',
            [$wallets, $wallets]
        );
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
     * Check if user has transactions
     *
     * @return bool
     */
    public function hasTransactions(): bool
    {
        return count($this->transactions()->get()->toArray());
    }

    /**
     * Check if user has any active wallet
     *
     * @return bool
     */
    public function hasAnyActiveWallet(): bool
    {
        return count($this->activeWallets());
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
     * Check if user has wallet of any status
     *
     * @return bool
     */
    public function hasWallet(): bool
    {
        return count($this->wallets);
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
            return $this->ownsWallet($item->wallet);
        }
        if ($item instanceof Wallet) {
            return $this->ownsWallet($item);
        }

        Log::warning('Invalid argument type for ownership check.');
        return false;
    }

    /**
     * Check if current user owns the provided wallet.
     * Note: null wallets are owned by everybody.
     *
     * @param Wallet|null $wallet
     * @return bool
     */
    private function ownsWallet(Wallet $wallet = null): bool
    {
        if ($wallet === null) {
            return false;
        }
        return (string) $this->id === (string) $wallet->user_id;
    }
}
