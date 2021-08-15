<x-datatable :dataTable="$dataTable" :shouldRenderTable="Auth::user()->hasTransfers()">
  <x-slot name="title">
    {{ __('Transfers') }}
  </x-slot>
  <x-slot name="createLink">
    {{ route('transfer.view.create') }}
  </x-slot>
</x-datatable>
