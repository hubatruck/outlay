<?php

namespace App\DataHandlers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class ChartDataHandler
{
    /**
     * Date format used to display dates
     */
    public const DATE_FORMAT = 'Y-m-d';

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
     * @param int $dataPrecision
     */
    public function __construct($data = null, int $dataPrecision = 2)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        $this->data = $data;
        $this->dataPrecision = $dataPrecision;
    }

    /**
     * Create a new instance from a give data
     * This function eases usage of the class, by adding the ability to call statically the constructor,
     * instead of going the regular $someVar = new ChartDataHandler(...) way
     *
     * @param Collection|array|null $data
     * @return ChartDataHandler
     */
    public static function from($data = null): ChartDataHandler
    {
        return static::newInstance($data);
    }

    /**
     * Creates a new instance of this class
     *
     * @param Collection|array|null $data
     * @return ChartDataHandler
     */
    protected static function newInstance($data = null): ChartDataHandler
    {
        return new ChartDataHandler($data);
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
        $cutter = 10 ** $this->dataPrecision;
        $this->data = array_map(static function ($item) use ($cutter) {
            if (is_array($item)) {
                foreach (['in', 'out'] as $key) {
                    $item[$key] = floor($item[$key] * $cutter) / $cutter;
                }
            } else {
                $item = floor($item * $cutter) / $cutter;
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
     * Fill out the keys of the array, so each day of the month is present
     *
     * @return $this
     */
    public function addMissingDays(): ChartDataHandler
    {
        $cutter = 10 ** $this->dataPrecision;
        $this->data = $this->eachDayOfTheMonth(function (Carbon $date) use ($cutter) {
            return floor(($this->data[$date->format(self::DATE_FORMAT)] ?? 0) * $cutter) / $cutter;
        });
        return $this;
    }

    /**
     * Do something with each day of the month
     *
     * @param callable $transformerCallback
     * @return array
     */
    protected function eachDayOfTheMonth(callable $transformerCallback): array
    {
        $newData = [];
        $period = CarbonPeriod::create(date('Y-m-01'), $this->lastDate());
        foreach ($period as $day) {
            $newData[] = $transformerCallback($day);
        }

        return $newData;
    }

    /**
     * Which day should be the last day of the month, that we display
     *
     * @return string
     */
    protected function lastDate(): string
    {
        return date('Y-m-d');
    }

    /**
     * Fill data array with the days of the month
     *
     * @return $this
     */
    public function daysOfMonth(): ChartDataHandler
    {
        $this->data = $this->eachDayOfTheMonth(function (Carbon $date) {
            return $date->format(self::DATE_FORMAT);
        });
        return $this;
    }
}
