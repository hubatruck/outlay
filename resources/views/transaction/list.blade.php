@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Transactions') }}</x-page-title>
  <div class="uk-card-body">
    @if (!Auth::user()->hasWallet())
      <div class="alert alert-warning">
        {{ __('You don\'t have any wallet connected to you account. Transactions feature is not available.') }}
        <br/>
        <a class="alert-link" href="{{ route('wallet.view.create') }}">
          {{ __('Create a wallet by clicking here.') }}
        </a>
      </div>
    @elseif(!Auth::user()->hasAnyActiveWallet())
      <div class="alert alert-warning">
        {{ __('You don\'t have any wallet marked as active. Transaction creation is unavailable.') }}
        <br/>
        <a class="alert-link" href="{{ route('wallet.view.all') }}">
          {{ __('Activate a wallet by clicking here.') }}
        </a>
      </div>
    @endif
    @if (Auth::user()->hasTransactions())
      {!! $dataTable->table(['class' => 'uk-table uk-table-response uk-table-divider uk-table-hover dt-responsive uk-margin-remove', 'width' => '100%'], true) !!}
    @else
      {{ __('Nothing here...') }}
      @if(Auth::user()->hasAnyActiveWallet())
        <a href="{{ route('transaction.view.create') }}">{{ __('Create') }}.</a>
      @endif
    @endif
  </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
