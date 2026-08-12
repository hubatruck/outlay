<?php

namespace App\Rules;

use App\Models\Wallet;
use Illuminate\Contracts\Validation\Rule;

/**
 * Determine if a provided wallet ID is valid
 */
class WalletExistsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if the provided wallet exists
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return Wallet::withTrashed()->find($value) !== null;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('The provided wallet does not exist.');
    }
}
