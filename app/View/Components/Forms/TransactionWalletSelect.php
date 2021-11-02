<?php

namespace App\View\Components\Forms;

use App\Models\Transaction;
use App\Models\Wallet;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TransactionWalletSelect extends Component
{
    public array|Transaction|null $transaction;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array|Transaction|null $transaction = null)
    {
        $this->transaction = $transaction;
    }

    public function isWalletUsable(Wallet $wallet): bool
    {
        return $wallet->deleted_at === null || $this->isTransactionWallet($wallet);
    }

    private function isTransactionWallet(Wallet $wallet): bool
    {
        return isset($this->transaction['wallet_id']) && (string) $this->transaction['wallet_id'] === (string) $wallet->id;
    }

    public function shouldSetAsSelected(Wallet $wallet): bool
    {
        return $this->isTransactionWallet($wallet) || $this->isOldWallet($wallet) || $this->isWalletInSession($wallet);
    }

    private function isOldWallet(Wallet $wallet): bool
    {
        return old('wallet_id') !== null && (string) $wallet->id === old('wallet_id');
    }

    private function isWalletInSession(Wallet $wallet): bool
    {
        $sessionVariable = session('transaction')['wallet_id'] ?? null;
        return isset($sessionVariable) && (string) $wallet->id === $sessionVariable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render(): View|string|Closure
    {
        return view('components.forms.transaction-wallet-select');
    }
}
