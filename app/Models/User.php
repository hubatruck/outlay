<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;

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
        return $this->hasManyThrough(Transaction::class, Wallet::class)->withTrashedParents();
    }

    /**
     * Check if user has transactions
     *
     * @return bool
     */
    public function hasTransactions(): bool
    {
        return count($this->transactions);
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
        return count($this->wallets);
    }

    /**
     * Che
     * @param Transaction|Wallet|null $item
     * @return bool
     */
    public function owns($item): bool
    {
        if ($item instanceof Transaction) {
            $item = $item->wallet;
        }
        if ($item instanceof Wallet) {
            return (string)$this->id === (string)$item->user_id;
        }

        Log::warning('Invalid argument type for ownership check.');
        return false;
    }
}
