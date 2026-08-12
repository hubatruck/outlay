<?php

namespace App\DataTables;

use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class TransfersDataTable extends DataTableBase
{
    protected array $dateColumns = ['transfer_date'];

    /**
     * Build DataTable class.
     *
     * @param  mixed  $query  Results from query() method.
     */
    public function dataTable(mixed $query): DataTableAbstract
    {
        return parent::dataTable($query)
            ->editColumn('transfer_date', fn ($row) => $row->transfer_date->translatedFormat('Y/m/d, l'))
            ->editColumn('from_wallet_name', fn (Transfer $row) => $this->getWalletNameFor($row->fromWallet))
            ->editColumn('to_wallet_name', fn (Transfer $row) => $this->getWalletNameFor($row->toWallet));
    }

    /**
     * Get styled name for a wallet.
     * If the user does not own the wallet, we add the wallet owner's name too.
     */
    private function getWalletNameFor(?Wallet $wallet = null): string
    {
        return $wallet !== null ? walletNameWithOwner($wallet) : __('[ERR:DELETED]');
    }

    /**
     * Get query source of dataTable
     */
    public function queryBase(): HasMany
    {
        return Auth::user()->transfers()
            ->with(['toWallet', 'fromWallet', 'toWallet.user', 'fromWallet.user'])
            ->select(['transfers.*', 'wallets_to.name as to_wallet_name', 'wallets_from.name as from_wallet_name']);
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): Builder
    {
        $buttons = $this->getButtons(Auth::user()->hasAnyActiveWallet());

        return $this->sharedHtmlBuild($buttons)
            ->orderBy(4)
            ->setTableId('transfers-table');
    }

    /**
     * Get columns.
     */
    protected function getColumns(): array
    {
        return [
            Column::make('description')->title(__('Description')),
            Column::make('amount')->title(__('Amount')),
            Column::make('from_wallet_name')->title(__('From'))->name('wallets_from.name'),
            Column::make('to_wallet_name')->title(__('To'))->name('wallets_to.name'),
            Column::make('transfer_date')->title(__('Date')),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'Transfers_'.date('YmdHis');
    }
}
