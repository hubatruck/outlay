<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<float|int,int>
 */
class Amount implements CastsAttributes
{
    /**
     * Change amount to display value
     */
    public function get($model, string $key, $value, array $attributes): float|int
    {
        return $value / 100;
    }

    /**
     * Change amount to store value
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return (int) ($value * 100);
    }
}
