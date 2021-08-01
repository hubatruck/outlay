<?php

namespace App\DataTables;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
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
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y/m/d H:i');
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
            ->select('transactions.*', 'transaction_types.name', 'wallets.name'); /// to prevent createdAt ambiguity
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->responsive()
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            )->parameters([
                'buttons' => ['create', 'export', 'print', 'reset'],
                'language' => ['url' => url('/vendor/datatables/lang/datatables.' . config('app.locale') . '.json')]
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
            __('Scope') => ['name' => 'transactions.scope', 'data' => 'scope',],
            __('Amount') => ['name' => 'transactions.amount', 'data' => 'amount',],
            __('Type') => ['name' => 'transaction_types.name', 'data' => 'transaction_type.name'],
            __('Wallet') => ['name' => 'wallet.name', 'data' => 'wallet.name'],
            __('Date') => ['name' => 'transactions.created_at', 'data' => 'created_at', 'formatted' => true]
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