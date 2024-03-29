<div class="uk-card uk-card-default uk-child-width-1-1">
  <div class="uk-card-header">
    <h4 class="uk-h4">{{  __('Balance chart') }}</h4>
  </div>
  <div class="uk-card-body">
    <div class="uk-width-1-1">
      {!! $balanceDailyChart->container() !!}
    </div>
  </div>
</div>

<div class="uk-card uk-card-default uk-child-width-1-1">
  <div class="uk-card-header">
    <h4 class="uk-h4">{{  __('Transaction charts') }}</h4>
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

<div class="uk-card uk-card-default uk-child-width-1-1">
  <div class="uk-card-header">
    <h4 class="uk-h4">{{  __('Transfer charts') }}</h4>
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

{{ $balanceDailyChart->script() }}
{{ $transactionDailyChart->script() }}
{{ $transactionTypeChart->script() }}
{{ $transferDailyChart->script() }}
{{ $transferTypeChart->script() }}
{{ $transferWalletChart->script() }}
