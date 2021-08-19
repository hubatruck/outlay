<?php

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
    function reducePrecision(float $number, $decimalPlaces = 2)
    {
        $cutter = 10 ** $decimalPlaces;
        return floor($number * $cutter) / $cutter;
    }
}
