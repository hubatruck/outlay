<x-datatable :dataTable="$dataTable" :shouldRenderTable="Auth::user()->hasTransactions()">
  <x-slot name="title">
    {{ __('Transactions') }}
  </x-slot>
  <x-slot name="createLink">
    route('transaction.view.create')
  </x-slot>
</x-datatable>
