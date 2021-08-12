<?php

namespace App\DataTables;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use ErrorException;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

abstract class DataTableBase extends DataTable
{
/// FIXME: AFTER RESIZING AND HOLDING THE TABLE, IT GETS SORTED
    /**
     * Script needed for the table to work correctly
     */
    protected const INIT_FUNCTION = "function(){
        var api = this.api();
        var resizing = false;
        /// column footer search boxes
        api.columns().every(function (colIdx) {
            var column = this;
            var input = document.createElement(\"input\");
            input.classList = 'form-control form-control-sm';
            input.placeholder = column.header().title;
            $(input).appendTo($(column.footer()).empty())
            .on('change', function () {
               column.search($(this).val(), false, false, true).draw();
            });
        });

        /// reset footer search boxes on 'reset' button
//        api.on('stateLoaded.dt', (e, settings,data)=>{
//            api.columns().every(function (colIdx) {
//                  input.value = this.state().columns[column.index()].search.search;
//                var colSearch = this.state().columns[colIdx].search;
//                $('input', this.columns(colIdx).footer()).val(colSearch.search);
//            });
//        });
    }";

    /**
     * Fields that should be handled as dates
     *
     * @var array
     */
    protected $dateColumns = [];

    /**
     * Apply custom filters to DB query
     *
     * @return HasManyThrough|\Illuminate\Database\Query\Builder|\Illuminate\Database\Schema\Builder
     */
    public function query()
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
     * @return HasManyThrough|\Illuminate\Database\Query\Builder|\Illuminate\Database\Schema\Builder
     */
    abstract protected function queryBase();

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
            $format = 'Y-m-d H:i:s';

            try {
                [$from, $to] = explode(' to ', $reqDateRange);
            } catch (ErrorException $e) {
                /// In case the user selects a single day
                $to = $from = $reqDateRange;
            }

            try {
                $dateRange[] = Carbon::parse($from)->startOfDay()->format($format);
                $dateRange[] = Carbon::parse($to)->endOfDay()->format($format);
            } catch (InvalidFormatException $e) {
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
            Button::make('reset'),
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
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->responsive()
            ->buttons($buttons)
            ->autoWidth()
            ->colReorder()
            ->fixedHeader()
            ->scrollX(1000)
            ->orderMulti()
            ->language(['url' => url('/vendor/datatables/lang/datatables.' . config('app.locale') . '.json')])
            ->parameters([
                'initComplete' => self::INIT_FUNCTION,
            ]);
    }

    /**
     * This function should return the columns displayed by the table
     *
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * Filter a date field
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param string $column
     */
    protected function dateFilter(\Illuminate\Database\Eloquent\Builder $query, string $keyword, string $column): void
    {
        $keyword = preg_replace('/,./', '-', $keyword);
        try {
            $date = Carbon::parse($keyword);
        } catch (InvalidFormatException $e) {
            $date = Carbon::parse('0000-00-00');
        }
        $query->whereBetween($column, [
            $date->startOfDay()->format('Y-m-d H:i:s'),
            $date->endOfDay()->format('Y-m-d H:i:s'),
        ]);
    }
}
