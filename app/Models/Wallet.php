<?php

namespace App\Models;

use App\Feedbacks\WalletFeedback;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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
     * Transactions belonging to this wallet
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
