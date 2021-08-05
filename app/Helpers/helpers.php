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
