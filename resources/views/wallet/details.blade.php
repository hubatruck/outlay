@extends('layouts.app')

@php
  $hasTransfers = $wallet->hasTransfers();
  $hasTransactions = $wallet->hasTransactions()
@endphp

@section('content')
  <x-page-title>{{ __('Wallet details for :wallet', ['wallet' => $wallet->name ?? 'ERR:UNDEFINED']) }}</x-page-title>

  <div class="uk-card-body uk-padding-remove">
    <div class="uk-card uk-card-body">
      <div class="uk-text-large">
        @php($cb = $wallet->getBalanceBetween(null, currentDayOfTheMonth()))
        {{ __('Current balance') }}:
        <div class="uk-text-bolder uk-inline @if($cb < 0) uk-text-danger @else uk-text-success @endif">{{ $cb }}</div>
      </div>
    </div>
    <div class="uk-card uk-card-default @if ($hasTransactions || $hasTransfers)uk-margin-medium-bottom @endif">
      <div class="uk-card-header"><h4 class="uk-h4">{{ __('Manage wallet') }}</h4></div>
      <div class="uk-card-body uk-button-group">
        @if(config('app.debug'))
          <a class="uk-button uk-button-danger uk-button-large uk-margin-medium"
             href="{{ route('wallet.view.debug', ['id' => $wallet->id])  }}"
          > DEBUG Wallet </a><br>
        @endif

        <x-quick-create-button :wallet="$wallet" :targetType="'transaction'"></x-quick-create-button>
        <a
          class="uk-button uk-button-secondary"
          href="{{ route('wallet.view.update', ['id' => $wallet->id]) }}"
        >
          <span class="uk-margin-small" uk-icon="pencil"></span>
          {{ __('Edit') }}
        </a>
        <a
          class="uk-button uk-button-default"
          href="{{ route('wallet.manage.toggle_hidden', ['id' => $wallet->id]) }}"
        >
          @if ($wallet->deleted_at)
            <span class="uk-margin-small" uk-icon="play"></span>
            {{ __('Reactivate') }}
          @else
            <span class="uk-margin-small" uk-icon="ban"></span>
            {{ __('Hide') }}
          @endif
        </a>
        <a
          class="uk-button uk-button-danger @if($wallet->hasTransactions())uk-link-muted @endif"
          href="{{ $hasTransactions ? '#' : route('wallet.manage.delete', ['id' => $wallet->id]) }}"
          @if($hasTransactions)
          uk-tooltip="{{ __('Wallet has transactions linked to it. Cannot be deleted.') }}"
          @endif
        >
          <span class="uk-margin-small" uk-icon="trash"></span>
          {{ __('Delete') }}
        </a>
      </div>
    </div>

    @if ($hasTransactions || $hasTransfers)
      <x-chart-range-picker :chartContainer="'#charts'" :walletID="$wallet->id"/>
      <div id="charts"></div>
    @endif
  </div>
@endsection

@if ($hasTransactions || $hasTransfers)
  @push('scripts')
    <script src="{{ @asset('vendor/larapex-charts/apexcharts.js') }}"></script>
  @endpush
@endif
