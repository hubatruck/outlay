@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Dashboard') }}</x-page-title>

  <div class="uk-card-body">
    <div class="uk-grid-match uk-child-width-expand@l" uk-grid>
      <div>
        <div class="uk-card uk-card-default uk-card-hover">
          <div class="uk-card-body">
            <h3 class="uk-card-title">{{ __('Transactions') }}</h3>
            <p>{{ __('View your transactions.') }}</p>
          </div>
          <div class="uk-card-footer">
            <a
              href="{{ route('transaction.view.all')}}"
              class="uk-button uk-button-primary"
            > {{ __('Visit') }} </a>
          </div>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-default uk-card-hover">
          <div class="uk-card-body">
            <h3 class="uk-card-title">{{ __('Transfers') }}</h3>
            <p>{{ __('View incoming and outgoing transfers.') }}</p>
          </div>
          <div class="uk-card-footer">
            <a
              href="{{ route('transfer.view.all')}}"
              class="uk-button uk-button-primary"
            > {{ __('Visit') }} </a>
          </div>
        </div>
      </div>

      <div>
        <div class="uk-card uk-card-default uk-card-hover">
          <div class="uk-card-body">
            <h3 class="uk-card-title">{{ __('Wallets') }}</h3>
            <p>{{ __('View wallets linked to your account.') }}</p>
          </div>
          <div class="uk-card-footer">
            <a
              href="{{ route('wallet.view.all')}}"
              class="uk-button uk-button-primary"
            > {{ __('Visit') }} </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
