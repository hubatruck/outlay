<?php

namespace App\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
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
            ->smart(true)
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
     * @param Transaction $model
     * @return EloquentBuilder
     */
    public function query(Transaction $model): EloquentBuilder
    {
        return $model->newQuery()
            ->with(['transactionType', 'wallet'])
            ->join('wallets', 'wallet_id', '=', 'wallets.id')
            ->join('transaction_types', 'transaction_type_id', '=', 'transaction_types.id')
            ->whereIn('wallet_id', function ($query) {
                /// https://stackoverflow.com/a/16815955
                $query->select('id')->from('wallets')->where('user_id', '=', Auth::user()->id ?? '-1');
            })
            ->select(['transactions.*', 'transaction_types.name as type', 'wallets.name as wallet_name']); /// to prevent createdAt ambiguity
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
            ->setTableId('transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->responsive()
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
            __('Scope') => ['name' => 'transactions.scope', 'data' => 'scope'],
            __('Amount') => ['name' => 'transactions.amount', 'data' => 'amount'],
            __('Type') => ['name' => 'transaction_types.name', 'data' => 'type'],
            __('Wallet') => ['name' => 'wallet.name', 'data' => 'wallet_name'],
            __('Date') => ['name' => 'transaction_date', 'data' => 'transaction_date'],
            __('Actions') => ['data' => 'actions'],
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
