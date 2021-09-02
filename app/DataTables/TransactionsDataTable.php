<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class TransactionsDataTable extends DataTableBase
{

    protected array $dateColumns = ['transaction_date'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return parent::dataTable($query)
            ->addColumn('actions', function ($row) {
                return View::make('components.transaction-dt-actions')->with([
                    'editURL' => route('transaction.view.update', ['id' => $row->transaction_id]),
                    'deleteURL' => route('transaction.data.delete', ['id' => $row->transaction_id]),
                ]);
            })
            ->rawColumns(['actions'])
            ->blacklist(['actions'])
            ->editColumn('transaction_date', function ($row) {
                return $row->transaction_date->translatedFormat('Y/m/d, l');
            })
            ->editColumn('type', function ($row) {
                return __($row->transactionType->name);
            });
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
     * Get query source of dataTable.
     *
     * @return HasManyThrough
     */
    protected function queryBase(): HasManyThrough
    {
        /// https://stackoverflow.com/a/63285943
        return Auth::user()->transactions()
            ->with(['transactionType:id,name', 'wallet:id,name'])
            ->select('transactions.id as transaction_id', 'transactions.*');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            Column::make('scope')->title(__('Scope'))->name('transactions.scope'),
            Column::make('amount')->title(__('Amount'))->name('transactions.amount'),
            Column::make('type')->title(__('Type'))->name('transactionType.name'),
            Column::make('wallet.name')->title(__('Wallet'))->name('wallets.name'),
            Column::make('transaction_date')->title(__('Date')),
            $this->actionsColumn(),
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
