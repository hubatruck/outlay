<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
        return date('Y-m-d');
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
        $format = 'Y-m-d';
        return $range->first()->format($format) . ' - ' . $range->last()->format($format);
    }
}
