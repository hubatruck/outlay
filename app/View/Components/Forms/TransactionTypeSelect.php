<?php

namespace App\View\Components\Forms;

use App\Models\Transaction;
use App\Models\TransactionType;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TransactionTypeSelect extends Component
{
    public array|Transaction|null $transaction;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array|Transaction|null $transaction)
    {
        $this->transaction = $transaction;
    }

    public function shouldSelectType(TransactionType $transactionType): bool
    {
        return $this->isSetType($transactionType) || $this->isSessionSelectedType($transactionType);
    }

    public function isSetType(TransactionType $transactionType): bool
    {
        return isset($this->transaction['transaction_type_id']) && (string) $transactionType->id === $this->transaction['transaction_type_id'];
    }

    public function isSessionSelectedType(TransactionType $transactionType): bool
    {
        $sessionVariable = session('transaction')['transaction_type_id'] ?? null;
        return $sessionVariable !== null && (string) $transactionType->id === $sessionVariable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render(): View|string|Closure
    {
        return view('components.forms.transaction-type-select');
    }
}
