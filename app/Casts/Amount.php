<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Amount implements CastsAttributes
{
    /**
     * Change amount to display value
     * @param $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return float|int
     */
    public function get($model, string $key, $value, array $attributes): float|int
    {
        return $value / 100;
    }

    /**
     * Change amount to store value
     * @param $model
     * @param string $key
     * @param $value
     * @param array $attributes
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return (int) ($value * 100);
    }
}
