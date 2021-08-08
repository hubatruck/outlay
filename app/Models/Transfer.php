<?php

namespace App\Models;

use Database\Factories\TransferFactory;
use Eloquent;
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
 * @property string $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Wallet|null $fromWallet
 * @property-read Wallet|null $toWallet
 * @method static Builder|Transfer newModelQuery()
 * @method static Builder|Transfer newQuery()
 * @method static Builder|Transfer query()
 * @method static Builder|Transfer whereCreatedAt($value)
 * @method static Builder|Transfer whereDescription($value)
 * @method static Builder|Transfer whereFromWalletId($value)
 * @method static Builder|Transfer whereId($value)
 * @method static Builder|Transfer whereToWalletId($value)
 * @method static Builder|Transfer whereTransferDate($value)
 * @method static Builder|Transfer whereUpdatedAt($value)
 * @mixin Eloquent
 * @method static Builder|Transfer whereAmount($value)
 * @method static TransferFactory factory(...$parameters)
 */
class Transfer extends Model
{
    use HasFactory;

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
}
