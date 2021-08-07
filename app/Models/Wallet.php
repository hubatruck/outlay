<?php

namespace App\Models;

use App\Feedbacks\WalletFeedback;
use Database\Factories\WalletFactory;
use Eloquent;
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
 * @property int $is_card
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
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
 * @method static Builder|Wallet whereIsCard($value)
 * @method static Builder|Wallet whereName($value)
 * @method static Builder|Wallet whereNotes($value)
 * @method static Builder|Wallet whereUpdatedAt($value)
 * @method static Builder|Wallet whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Wallet withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Wallet withoutTrashed()
 * @mixin Eloquent
 */
class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Fields that are fillable for the model
     * @var string
     */
    protected $fillable = ['user_id', 'name', 'balance', 'notes', 'is_card'];

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
     * Get the user who owns this wallet
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transactions belonging to the current user
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)
            ->orWhereHas('destinationWallet', function ($query) {
                return $query->where('destination_wallet_id', '=', $this->id);
            });
    }
}
