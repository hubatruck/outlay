<?php

namespace App\Models;

use App\Feedbacks\TransactionFeedback;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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

    protected $appends = ['wallet_name', 'type'];

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
     * Append the wallet name
     * @return string
     */
    public function getWalletNameAttribute(): string
    {
        return Wallet::withTrashed()
                ->find($this->wallet_id)
                ->name ?? 'ERR::WALLET_404';
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
     * @param string $value
     */
    public function setTransactionDateAttribute(string $value): void
    {
        $this->attributes['transaction_date'] = (Carbon::parse($value)->format('Y-m-d') . ' 03:00:00');
    }
}
