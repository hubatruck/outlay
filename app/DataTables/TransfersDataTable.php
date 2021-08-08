<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransfersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->smart()
            ->editColumn('transfer_date', function ($row) {
                return $row->transfer_date->translatedFormat('Y/m/d, l');
            })
            ->addColumn('from_wallet_name', function ($row) {
                return $row->fromWallet->name;
            })
            ->addColumn('to_wallet_name', function ($row) {
                return $row->toWallet->name;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @return HasManyThrough
     */
    public function query(): HasManyThrough
    {
        return Auth::user()->transfers()
            ->with(['toWallet', 'fromWallet']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html(): Builder
    {
        $buttonArr = [];
        if (Auth::user()->hasAnyActiveWallet()) {
            $buttonArr[] = Button::make('create');
        }
        $buttonArr[] = Button::make('export');
        $buttonArr[] = Button::make('print');
        $buttonArr[] = Button::make('reset');

        return $this->builder()
            ->setTableId('transfers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->responsive()
            ->orderBy(3) /// transfer_date
            ->buttons($buttonArr)
            ->parameters([
                'language' => [
                    'url' => url('/vendor/datatables/lang/datatables.' . config('app.locale') . '.json'),
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make('description')->title(__('Description')),
            Column::make('from_wallet_name')->title(__('From')),
            Column::make('to_wallet_name')->title(__('To')),
            Column::make('transfer_date')->title(__('Date')),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Transfers_' . date('YmdHis');
    }
}
