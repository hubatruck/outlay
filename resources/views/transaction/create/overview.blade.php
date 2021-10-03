@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Transaction creator') }} - {{ __('Overview') }}</x-page-title>
  <div class="uk-card-body">
    <form action="{{ route('transaction.data.create.overview') }}" method="POST">
      @csrf

      @if (isset($transaction['scope'],$transaction['amount']))
        <table class="uk-table uk-table-striped uk-table-hover">
          <caption><h4>{{ __('Items') }}</h4>
          </caption>
          <thead>
          <tr>
            <th id="scope" class="uk-table-shrink">{{ __('Scope') }}</th>
            <th id="amount" class="uk-table-shrink">{{ __('Amount') }}</th>
          </tr>
          </thead>
          <tbody>
          @foreach($transaction['scope'] as $stepper)
            <tr>
              <td>{{ $transaction['scope'][$loop->index] }}</td>
              <td>{{ $transaction['amount'][$loop->index] }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @else
        <x-transaction-overview-error
          provideThis="at least one transaction item"
        />
      @endif
      <x-buttons.change :url="route('transaction.view.create.items')"/>
      <hr>

      @if(isset($transaction['wallet_id'], $transaction['transaction_type_id'], $transaction['transaction_date']))
        <table class="uk-table uk-table-striped uk-table-hover">
          <caption><h4>{{ __('Payment details') }}</h4></caption>
          <thead>
          <th id="name">{{ __('Name') }}</th>
          <th id="value">{{ __('Selected value') }}</th>
          </thead>
          <tbody>
          <tr>
            <td>{{ __('Wallet') }}:</td>
            <td>{{ walletNameWithOwner(\App\Models\Wallet::all()->find($transaction['wallet_id'])) }}</td>
          </tr>
          <tr>
            <td>{{ __('Type') }}:</td>
            <td>{{ __(\App\Models\TransactionType::findOrFail($transaction['transaction_type_id'])->name) }}</td>
          </tr>
          <tr>
            <td>{{ __('Date') }}:</td>
            <td>{{ $transaction['transaction_date'] }}</td>
          </tr>
          </tbody>
        </table>
      @else
        <x-transaction-overview-error
          provideThis="payment details"
        />
      @endif
      <x-buttons.change :url="route('transaction.view.create.payment')"/>
      <hr>

      <x-buttons.submit-form/>
    </form>
  </div>
@endsection
