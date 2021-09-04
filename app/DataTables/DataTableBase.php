<?php

namespace App\DataTables;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

abstract class DataTableBase extends DataTable
{
    /**
     * ID of the date-picker element
     */
    public const DATE_RANGE_ID = 'date-range-input';

    /**
     * Fields that should be handled as dates
     *
     * @var array
     */
    protected array $dateColumns = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->smart();
    }

    /**
     * Apply custom filters to DB query
     *
     * @return HasMany|HasManyThrough
     */
    public function query(): HasManyThrough|HasMany
    {
        $query = $this->queryBase();
        $range = $this->parseDateRange();

        if ($range) {
            $this->applyDateRange($query, $this->dateColumns, $range);
        }

        return $query;
    }

    /**
     * Query for getting data from the database
     *
     * @return HasManyThrough|HasMany
     */
    abstract protected function queryBase(): HasManyThrough|HasMany;

    /**
     * Parse data range from request
     *
     * @return array
     */
    private function parseDateRange(): array
    {
        $reqDateRange = $this->request()->get('date_range');
        $dateRange = [];

        if ($reqDateRange) {
            $format = globalDateFormat() . ' H:i:s';

            try {
                [$from, $to] = explode(' - ', $reqDateRange);
            } catch (Exception) {
                $to = $from = $reqDateRange;
            }

            try {
                $dateRange[] = Carbon::parse($from)->startOfDay()->format($format);
                $dateRange[] = Carbon::parse($to)->endOfDay()->format($format);
            } catch (InvalidFormatException) {
                $dateRange = [];
            }
        }
        return $dateRange;
    }

    /**
     * Apply between query for specified columns
     *
     * @param $query
     * @param array $columns
     * @param array $range Format: ['YYYY-MM-DD HH:II:SS', 'YYYY-MM-DD HH:II:SS']
     */
    private function applyDateRange($query, array $columns, array $range): void
    {
        foreach ($columns as $col) {
            $query->whereBetween($col, $range);
        }
    }

    protected function getButtons($addCreateButton = true): array
    {
        $buttons = [
            Button::make(['extend' => 'export']),
            Button::make('print'),
            Button::make(['extend' => 'colvis', 'postfixButtons' => ['colvisRestore']]),
        ];

        if ($addCreateButton) {
            array_unshift($buttons, Button::make(['extend' => 'create', 'className' => 'btn-success text-white']));
        }
        return $buttons;
    }

    protected function sharedHtmlBuild(array $buttons = []): Builder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax('', null, $this->dateRangeHandler())
            ->dom(
                "<'row uk-grid'<'uk-width-1-1 uk-margin-small-bottom'B><'uk-width-1-2@s'f><'#dtc-filters.uk-width-1-2@s'>>" .
                "<'row uk-grid dt-merge-grid'<'uk-width-1-1'rt>>" .
                "<'row uk-grid dt-merge-grid uk-grid-collapse'<'uk-width-1-2@s'l><'uk-width-1-2@s'i><'uk-width-1-1'p>>"
            )
            ->responsive()
            ->buttons($buttons)
            ->autoWidth()
            ->colReorder()
            ->scrollX()
            ->orderMulti()
            ->language(['url' => url('/vendor/datatables/lang/datatables.' . config('app.locale') . '.json')])
            ->parameters([
                'initComplete' => $this->getInitCompleteFunction(),
            ]);
    }

    /**
     * This function should return the columns displayed by the table
     *
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * Add date picker's value to the request
     *
     * @return string[]
     */
    protected function dateRangeHandler(): array
    {
        return ['date_range' => '$("#' . self::DATE_RANGE_ID . '").val()'];
    }

    /**
     * initComplete script for DataTable instance
     *
     * @return string
     */
    protected function getInitCompleteFunction(): string
    {
        return View::make('skeletons.datatables-init', ['dateRangeID' => self::DATE_RANGE_ID])->render();
    }

    /**
     * Function to create actions column
     *
     * @return Column
     */
    protected function actionsColumn(): Column
    {
        return Column::make('actions')->title(__('Actions'))->orderable(false)->searchable(false)->printable(false)->exportable(false);
    }

    /**
     * Get decorated data as defined in datatables ajax response.
     * Overwrites the base class's function, by printing only the
     * current page visible in the datatable.
     *
     * @return array
     */
    protected function getAjaxResponseData(): array
    {
        $response = app()->call([$this, 'ajax']);
        $data = $response->getData(true);

        return $data['data'];
    }

    /**
     * Get mapped columns versus final decorated output.
     * Overwrites the base function, by displaying the columns in the order
     * set by the user in the datatable.
     *
     * @return array
     */
    protected function getDataForPrint(): array
    {
        $columns = $this->orderColumnsInRequestOrder();

        return $this->mapResponseToColumns($columns, 'printable');
    }

    /**
     * Orders the table columns based on the request
     *
     * @return Collection
     */
    private function orderColumnsInRequestOrder(): Collection
    {
        $columns = $this->printColumns();
        $order = array_flip(Arr::pluck($this->request->get('columns'), 'data'));
        $newColumns = array_fill(0, sizeof($columns), null);

        foreach ($columns->all() as $column) {
            $key = $column->data;
            $index = $order[$key];
            $newColumns[$index] = $column;
        }

        return collect($newColumns);
    }
}
