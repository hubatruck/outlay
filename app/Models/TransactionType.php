<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TransactionType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|TransactionType newModelQuery()
 * @method static Builder|TransactionType newQuery()
 * @method static Builder|TransactionType query()
 * @method static Builder|TransactionType whereCreatedAt($value)
 * @method static Builder|TransactionType whereDeletedAt($value)
 * @method static Builder|TransactionType whereId($value)
 * @method static Builder|TransactionType whereName($value)
 * @method static Builder|TransactionType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TransactionType extends Model
{
    use HasFactory;

    /// https://stackoverflow.com/a/53872157
    public static function transactionTypeCreate(string ...$types): void
    {
        foreach ($types as $type) {
            $tType = new TransactionType();
            $tType->setAttribute('name', $type);
            $tType->save();
        }
    }
}
