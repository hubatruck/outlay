<?php

use App\Models\Wallet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

if (!function_exists('previousUrlOr')) {
    /**
     * Get previous URL or fallback
     * @param string $fallback
     * @return string
     */
    function previousUrlOr(string $fallback = '/'): string
    {
        $previous = url()->previous();
        $current = url()->current();
        $login = route('login');

        return ($previous === $current || $previous === $login) ? $fallback : $previous;
    }
}

if (!function_exists('addSessionMsg')) {
    /**
     * Add a message to be displayed on screen
     *
     * @param array $message
     * @param bool $now Set to true, if you are adding a message without redirecting, just showing a view
     */
    function addSessionMsg(array $message, bool $now = false): void
    {
        if (session('messages')) {
            $messages = session('messages');
        } else {
            $messages = [];
        }

        $messages[] = $message;
        $messages = array_unique($messages, SORT_REGULAR);

        if ($now) {
            /// https://stackoverflow.com/a/31743850
            session()->now('messages', $messages);
        } else {
            session()->flash('messages', $messages);
        }
    }
}

if (!function_exists('reducePrecision')) {
    /**
     * Reduce floating point precision for $number to $decimalPlaces digits
     *
     * @param float $number
     * @param int $decimalPlaces
     * @return float|int
     */
    function reducePrecision(float $number, int $decimalPlaces = 2): float|int
    {
        $cutter = 10 ** $decimalPlaces;
        return floor($number * $cutter) / $cutter;
    }
}

if (!function_exists('currentDayOfTheMonth')) {
    /**
     * Returns what day should we consider as the last (current) day of the month
     *
     * @return bool|string
     */
    function currentDayOfTheMonth(): bool|string
    {
        return date(globalDateFormat());
    }
}

if (!function_exists('defaultChartRange')) {
    /**
     * Returns the default date range used for charts if none is provided
     *
     * @return CarbonPeriod last 7 days
     */
    function defaultChartRange(): CarbonPeriod
    {
        return CarbonPeriod::create(Carbon::now()->subWeek(), Carbon::now());
    }
}

if (!function_exists('defaultChartRangeAsFlatpickrValue')) {
    /**
     * Get the defaultChartRange() function's value as Flatpickr parseable
     * values
     *
     * @return string
     */
    function defaultChartRangeAsFlatpickrValue(): string
    {
        $range = defaultChartRange();
        $format = globalDateFormat();
        return $range->first()->format($format) . ' - ' . $range->last()->format($format);
    }
}

if (!function_exists('globalDateFormat')) {
    /**
     * The default date format used everywhere
     *
     * @return string
     */
    function globalDateFormat(): string
    {
        return 'Y-m-d';
    }
}

if (!function_exists('walletNameWithOwner')) {
    /**
     * Format wallet name, by adding the owner's name in parentheses (if it's the case)
     * The style for an external wallet is: owner name (?External wallet): wallet name
     * @param Wallet $wallet
     * @param bool $flagExternal Mark wallet as external
     * @return string
     */
    function walletNameWithOwner(Wallet $wallet, bool $flagExternal = false): string
    {
        $name = '';
        if (!Auth::user()->owns($wallet)) {
            $name = $wallet->user->name;
            if ($flagExternal) {
                $name .= ' (' . __('External wallet') . ')';
            }
            $name .= ': ';
        }
        $name .= $wallet->name;
        return $name;
    }
}
