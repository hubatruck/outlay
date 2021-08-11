<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class TransactionsDataTable extends DataTableBase
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
            ->addColumn('actions', function ($row) {
                $actions = '<div class="dropdown mx-auto">
                            <button class="btn dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                                ' . __('Actions') . '<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <li><a class="dropdown-item btn-outline-primray" href="'
                    . route('transaction.view.update', ['id' => $row->id]) . '">' . __('Edit')
                    . '</a></li>
                                <li><a class="dropdown-item btn-outline-danger" href="'
                    . route('transaction.data.delete', ['id' => $row->id]) . '">' . __('Delete')
                    . '</a></li>';
                if (config('app.debug')) {
                    $actions .= '<li><a class="dropdown-item btn-outline-primray" href="'
                        . route('transaction.view.debug', ['id' => $row->id]) . '">' . __('DEBUG')
                        . '</a></li>';
                }
                $actions .= '</ul></div>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->blacklist(['actions'])
            ->editColumn('transaction_date', function ($row) {
                return $row->transaction_date->translatedFormat('Y/m/d, l');
            })
            ->editColumn('type', function ($row) {
                return __($row->type);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @return HasManyThrough
     */
    public function query(): HasManyThrough
    {
        /// https://stackoverflow.com/a/63285943
        return Auth::user()->transactions()
            ->with(['transactionType:id,name', 'wallet:id,name']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html(): Builder
    {
        $buttons = $this->getButtons(Auth::user()->hasAnyActiveWallet());

        return $this->sharedHtmlBuild($buttons)
            ->setTableId('transactions-table');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make('scope')->title('Scope')->name('transactions.scope'),
            Column::make('amount')->title(__('Amount'))->name('transactions.amount'),
            Column::make('type')->title(__('Type'))->name('transactionType.name'),
            Column::make('wallet_name')->title(__('Wallet'))->name('wallet.name'),
            Column::make('transaction_date')->title(__('Date')),
            Column::make('actions')->title(__('Actions'))->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Transactions_' . date('YmdHis');
    }
}
