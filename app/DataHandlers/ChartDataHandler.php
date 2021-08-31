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
     *
     * @var CarbonPeriod
     */
    protected CarbonPeriod $range;

    /**
     * Stored data
     *
     * @var array|Collection|null
     */
    protected $data;

    /**
     * Floating point digit precision
     *
     * @var int
     */
    protected int $dataPrecision;

    /**
     * @param array|Collection|null $data
     * @param CarbonPeriod|null $range
     * @param int $dataPrecision
     */
    public function __construct($data = null, CarbonPeriod $range = null, int $dataPrecision = 2)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        $this->data = $data;
        $this->range = $range ?? CarbonPeriod::create(date('Y-m-01'), currentDayOfTheMonth());
        $this->dataPrecision = $dataPrecision;
    }

    /**
     * Create a new instance from a give data
     * This function eases usage of the class, by adding the ability to call statically the constructor,
     * instead of going the regular $someVar = new ChartDataHandler(...) way
     *
     * @param Collection|array|null $data
     * @param CarbonPeriod|null $range
     * @return ChartDataHandler
     */
    public static function from($data = null, CarbonPeriod $range = null): ChartDataHandler
    {
        return static::newInstance($data, $range);
    }

    /**
     * Creates a new instance of this class
     *
     * @param Collection|array|null $data
     * @param CarbonPeriod|null $range
     * @return ChartDataHandler
     */
    protected static function newInstance($data = null, CarbonPeriod $range = null): ChartDataHandler
    {
        return new ChartDataHandler($data, $range);
    }

    /**
     * Set the date range
     *
     * @param CarbonPeriod $range
     * @return $this
     */
    public function setRange(CarbonPeriod $range): ChartDataHandler
    {
        $this->range = $range;
        return $this;
    }

    /**
     * Set the floating point precision
     *
     * @param int|mixed $dataPrecision
     */
    public function setDataPrecision($dataPrecision): void
    {
        $this->dataPrecision = $dataPrecision;
    }

    /**
     * Shorter alias function for `$this->reduceDataPrecision()->get()`
     * @return array
     */
    public function reduceDPAndGet(): array
    {
        return $this->reduceDataPrecision()->get();
    }

    /**
     * Get the data
     *
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }

    /**
     * Reduces the precision of the stored data
     *
     * @return $this
     */
    public function reduceDataPrecision(): ChartDataHandler
    {
        $this->data = array_map(function ($item) {
            if (is_array($item)) {
                foreach (['in', 'out'] as $key) {
                    $item[$key] = reducePrecision($item[$key], $this->dataPrecision);
                }
            } else {
                $item = reducePrecision($item, $this->dataPrecision);
            }
            return $item;
        }, $this->data);
        return $this;
    }

    /**
     * Translate each data item, if possible
     *
     * @return $this
     */
    public function translate(): ChartDataHandler
    {
        $this->data = array_map(static function ($item) {
            return __($item);
        }, $this->data);
        return $this;
    }

    /**
     * Fill out the keys of the array, so each day of the range is present
     *
     * @param bool $convertKeysToEpochTime
     * @return $this
     */
    public function addMissingDays(bool $convertKeysToEpochTime = true): ChartDataHandler
    {
        if ($convertKeysToEpochTime && strpos(array_key_first($this->data), '-')) {
            $this->data = $this->keysToEpoch()->data;
        }

        $this->data = $this->eachDayOfTheRange(function (Carbon $date) {
            return $this->data[$date->getTimestampMs()] ?? 0;
        });
        return $this;
    }

    /**
     * Converts data keys to Epoch time
     *
     * @return $this
     * @throws UnexpectedValueException When key is not parseable
     */
    public function keysToEpoch(): ChartDataHandler
    {
        $newData = [];
        foreach ($this->data as $key => $value) {
            $newKey = strtotime($key);
            if (!$newKey) {
                throw new UnexpectedValueException("Failed to parse array key '$key' as date");
            }
            $newData[$newKey . '000'] = $value;
        }
        $this->data = $newData;

        return $this;
    }

    /**
     * Do something with each day of the given range
     *
     * @param callable $transformerCallback
     * @return array
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
        $this->data = $this->eachDayOfTheRange(function (Carbon $date) {
            return $date->getTimestampMs();
        });
        return $this;
    }

    /**
     * Get keys of the stored data
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }
}
