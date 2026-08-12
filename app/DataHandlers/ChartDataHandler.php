<?php

namespace App\DataHandlers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use UnexpectedValueException;

class ChartDataHandler
{
    /**
     * Date range of the data
     */
    protected CarbonPeriod $range;

    /**
     * Stored data
     */
    protected Collection|array|null $data;

    /**
     * Floating point digit precision
     */
    protected int $dataPrecision;

    public function __construct(array|Collection|null $data = null, ?CarbonPeriod $range = null, int $dataPrecision = 2)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        $this->data = $data;
        $this->range = $range ?? defaultChartRange();
        $this->dataPrecision = $dataPrecision;
    }

    /**
     * Create a new instance from a give data
     * This function eases usage of the class, by adding the ability to call statically the constructor,
     * instead of going the regular $someVar = new ChartDataHandler(...) way
     */
    public static function from(array|Collection|null $data = null, ?CarbonPeriod $range = null): ChartDataHandler
    {
        return static::newInstance($data, $range);
    }

    /**
     * Creates a new instance of this class
     */
    protected static function newInstance(array|Collection|null $data = null, ?CarbonPeriod $range = null): ChartDataHandler
    {
        return new ChartDataHandler($data, $range);
    }

    /**
     * Set the date range
     *
     * @return $this
     */
    public function setRange(CarbonPeriod $range): ChartDataHandler
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Set the floating point precision
     */
    public function setDataPrecision(int $dataPrecision): void
    {
        $this->dataPrecision = $dataPrecision;
    }

    /**
     * Get the data
     */
    public function get(): Collection|array|null
    {
        return $this->data;
    }

    /**
     * Translate each data item, if possible
     *
     * @return $this
     */
    public function translate(): ChartDataHandler
    {
        $this->data = array_map(static fn ($item) => __($item), $this->data);

        return $this;
    }

    /**
     * Fill out the keys of the array, so each day of the range is present
     *
     * @return $this
     */
    public function addMissingDays(bool $convertKeysToEpochTime = true): ChartDataHandler
    {
        if ($convertKeysToEpochTime && strpos(array_key_first($this->data), '-')) {
            $this->data = $this->keysToEpoch()->data;
        }

        $this->data = $this->eachDayOfTheRange(fn (Carbon $date) => $this->data[$date->getTimestampMs()] ?? 0);

        return $this;
    }

    /**
     * Converts data keys to Epoch time
     *
     * @return $this
     *
     * @throws UnexpectedValueException When key is not parseable
     */
    public function keysToEpoch(): ChartDataHandler
    {
        $newData = [];
        foreach ($this->data as $key => $value) {
            $newKey = strtotime($key);
            if (! $newKey) {
                throw new UnexpectedValueException("Failed to parse array key '$key' as date");
            }
            $newData[$newKey.'000'] = $value;
        }
        $this->data = $newData;

        return $this;
    }

    /**
     * Do something with each day of the given range
     */
    protected function eachDayOfTheRange(callable $transformerCallback): array
    {
        $newData = [];
        foreach ($this->range as $day) {
            $newData[] = $transformerCallback($day);
        }

        return $newData;
    }

    /**
     * Fill data array with the days of the given range
     *
     * @return $this
     */
    public function fillWithDaysOfRange(): ChartDataHandler
    {
        $this->data = $this->eachDayOfTheRange(
            fn ($date) => $date->getTimestampMs()
        );

        return $this;
    }

    /**
     * Get keys of the stored data
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }
}
