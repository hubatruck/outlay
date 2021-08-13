@extends('layouts.app')

@section('content')
  @if (session('status'))
    <div class="alert alert-success" role="alert">
      {{ session('status') }}
    </div>
  @endif
  <div class="uk-card-header">
    <h3 class="uk-card-title">{{ __('Dashboard') }}</h3>
  </div>
  <div class="uk-card-body">
    @if(config('app.debug'))
      <a
        class="uk-button uk-button-large uk-button-danger uk-margin-medium"
        href="{{ route('debug')  }}"
      > DEBUG User </a>
    @endif
    <div class="uk-grid-match uk-child-width-1-2@s" uk-grid>
      <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-hover">
          <h3 class="uk-card-title">{{ __('Transactions') }}</h3>
          <p>{{ __('View your transactions.') }}</p>
          <a
            href="{{ route('transaction.view.all')}}"
            class="uk-button uk-button-primary"
          > {{ __('Visit') }} </a>
        </div>
      </div>
      <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-hover">
          <h3 class="uk-card-title">{{ __('Transfers') }}</h3>
          <p>{{ __('View incoming and outgoing transfers.') }}</p>
          <a
            href="{{ route('transfer.view.all')}}"
            class="uk-button uk-button-primary"
          > {{ __('Visit') }} </a>
        </div>
      </div>

      <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-hover">
          <h3 class="uk-card-title">{{ __('Wallets') }}</h3>
          <p>{{ __('View wallets linked to your account.') }}</p>
          <a
            href="{{ route('transaction.view.all')}}"
            class="uk-button uk-button-primary"
          > {{ __('Visit') }} </a>
        </div>
      </div>
    </div>
  </div>
@endsection
