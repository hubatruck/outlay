<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Append the wallet name
     * @return string
     */
    public function getWalletNameAttribute(): string
    {
        return Wallet::find($this->wallet_id)->name;
    }

    /**
     * Append transaction type
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return TransactionType::find($this->transaction_type_id)->name;
    }
}
