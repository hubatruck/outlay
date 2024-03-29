<?php

namespace App\Http\Validators;

use App\Models\TransactionType;
use App\Rules\UserOwnsWalletRule;
use App\Rules\WalletIsActiveRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Validator;

class TransactionValidator
{
    /**
     * Validate everything, with regular input type (i.e. editing a single transaction)
     */
    public const EVERYTHING_REG = 0;

    /**
     * Same as EVERYTHING_REG, but don't check if the selected wallet is active
     */
    public const EVERYTHING_NO_ACTIVE_WALLET = 1;

    /**
     * Validate everything, but data is in bulk form (i.e. from create wizard)
     */
    public const EVERYTHING_WITH_ITEMS = 2;

    /**
     * Validate only the item array
     */
    public const ONLY_ITEM_ARR = 3;

    /*
     * Validate only the payment part (wallet_id, transaction_date, and the type_id)
     */
    public const ONLY_PAYMENT = 4;

    /**
     * Validate the request's data
     *
     * @param Request|array $requestOrData
     * @param int $type
     * @param bool $doRedirect
     * @return array|MessageBag
     */
    public static function validate(Request|array $requestOrData, int $type, bool $doRedirect = true): array|MessageBag
    {
        $rules = self::getRulesFor($type);

        if ($doRedirect) {
            return $requestOrData->validate($rules);
        }

        $fields = ($requestOrData instanceof Request) ? $requestOrData->all() : $requestOrData;
        $validator = Validator::make($fields, $rules);
        $errors = $validator->errors();

        return $errors->all() === [] ? $requestOrData : $errors;
    }

    /**
     * Get validation rules for a specific validation type
     *
     * @param int $type
     * @return array
     */
    public static function getRulesFor(int $type): array
    {
        $rules = [];

        $checkActiveWallet = ($type !== self::EVERYTHING_NO_ACTIVE_WALLET);
        if ($type !== self::ONLY_ITEM_ARR) {
            $rules = self::paymentValidatorRules($checkActiveWallet);
        }

        $scopeItemRule = 'required|string|max:255';
        $amountItemRule = 'required|numeric|min:0.01|max:999999.99';
        if ($type === self::EVERYTHING_NO_ACTIVE_WALLET || $type === self::EVERYTHING_REG) {
            $rules = array_merge($rules, [
                'scope' => $scopeItemRule,
                'amount' => $amountItemRule,
            ]);
        }

        if ($type === self::EVERYTHING_WITH_ITEMS || $type === self::ONLY_ITEM_ARR) {
            $rules = array_merge($rules, [
                'scope' => 'required|array|min:1',
                'scope.0' => $scopeItemRule,
                'scope.*' => $scopeItemRule,
                'amount' => 'required|array|min:1',
                'amount.0' => $amountItemRule,
                'amount.*' => $amountItemRule,
            ]);
        }

        return $rules;
    }

    /**
     * Generate payment validator rules
     *
     * @param bool $walletMustBeActive
     * @return array
     */
    private static function paymentValidatorRules(bool $walletMustBeActive = true): array
    {
        $walletRules = ['bail'];
        if ($walletMustBeActive) {
            $walletRules[] = new UserOwnsWalletRule();
            $walletRules[] = new WalletIsActiveRule();
            $walletRules[] = Auth::user()->hasAnyActiveWallet() ? 'required' : 'nullable';
        }
        return [
            'wallet_id' => $walletRules,
            'transaction_type_id' => [
                'required',
                Rule::in(TransactionType::all()->pluck('id')->toArray()),
            ],
            'transaction_date' => 'required|date|date_format:' . globalDateFormat(),
        ];
    }
}
