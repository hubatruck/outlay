@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Wallet details for :wallet', ['wallet' => $wallet->name ?? 'ERR:UNDEFINED']) }}</x-page-title>

  <div class="uk-card-body uk-padding-remove">
    <div class="uk-card uk-card-body">
      <div class="uk-text-large">
        {{ __('Current balance') }}:
        <div class="uk-text-bolder uk-inline">{{ $wallet->currentBalance }}</div>
      </div>
    </div>
    <div class="uk-card uk-card-default uk-margin-medium-bottom">
      <div class="uk-card-header"><h4 class="uk-h4">{{ __('Manage wallet') }}</h4></div>
      <div class="uk-card-body uk-button-group">
        @if(config('app.debug'))
          <a class="uk-button uk-button-danger uk-button-large uk-margin-medium"
             href="{{ route('wallet.view.debug', ['id' => $wallet->id])  }}"
          > DEBUG Wallet </a><br>
        @endif
        <a
          class="uk-button uk-button-primary @if($wallet->deleted_at)uk-link-muted @endif"
          href="{{ $wallet->deleted_at ? '#' : route('transaction.view.create', ['wallet_id' => $wallet->id]) }}"
          @if($wallet->deleted_at)
          uk-tooltip="{{ __('This wallet cannot be used for new transactions until reactivated.') }}"
          @endif
        >
          <span class="uk-margin-small" uk-icon="plus"></span>
          {{ __('Add transaction') }}
        </a>
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
          href="{{ $wallet->hasTransactions() ? '#' : route('wallet.manage.delete', ['id' => $wallet->id]) }}"
          @if($wallet->hasTransactions())
          uk-tooltip="{{ __('Wallet has transactions linked to it. Cannot be deleted.') }}"
          @endif
        >
          <span class="uk-margin-small" uk-icon="trash"></span>
          {{ __('Delete') }}
        </a>
      </div>
    </div>

    @if ($wallet->hasTransactions() || $wallet->hasTransfers())
      <div class="uk-card uk-card-default uk-child-width-1-1">
        <div class="uk-card-header">
          <h4 class="uk-h4">{{  __('Balance chart for :month', ['month' => __(date('F'))]) }}</h4>
        </div>
        <div class="uk-card-body">
          <div class="uk-width-1-1">
            {!! $balanceDailyChart->container() !!}
          </div>
        </div>
      </div>
    @endif

    @if ($wallet->hasTransactions())
      <div class="uk-card uk-card-default uk-child-width-1-1">
        <div class="uk-card-header">
          <h4 class="uk-h4">{{  __('Transaction charts for :month', ['month' => __(date('F'))]) }}</h4>
        </div>
        <div class="uk-card-body uk-child-width-1-2@l">
          <div class="uk-width-1-1@l">
            {!! $transactionDailyChart->container() !!}
          </div>
          <div>
            {!! $transactionTypeChart->container() !!}
          </div>
        </div>
      </div>
    @endif

    @if(count($wallet->transfers))

      <div class="uk-card uk-card-default uk-child-width-1-1">
        <div class="uk-card-header">
          <h4 class="uk-h4">{{  __('Transfer charts for :month', ['month' => __(date('F'))]) }}</h4>
        </div>
        <div class="uk-card-body uk-child-width-1-2@l">
          <div class="uk-width-1-1">
            {!! $transferDailyChart->container() !!}
          </div>
          <div class="uk-width-1-1 uk-child-width-1-2@l" uk-grid>
            <div>
              {!! $transferTypeChart->container() !!}
            </div>
            <div>
              {!! $transferWalletChart->container() !!}
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection

@push('scripts')
  <script src="{{ @asset('vendor/larapex-charts/apexcharts.js') }}"></script>
  {{ $balanceDailyChart->script() }}
  {{ $transactionDailyChart->script() }}
  {{ $transactionTypeChart->script() }}
  {{ $transferDailyChart->script() }}
  {{ $transferTypeChart->script() }}
  {{ $transferWalletChart->script() }}
@endpush
