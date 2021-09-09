@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Transaction creator') }} - {{ __('Payment details') }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="route('transaction.data.create.payment')"
      :cancelURL="route('transaction.view.all')"
    >
      <div
        id="has-errors"
        class="uk-flex uk-width-1-1"
        @unless ($errors->any())
        style="display: none"
        @endunless
      >
        <span class="uk-alert uk-alert-danger uk-text-bold uk-width-1-1">
          {{ __('Some fields are invalid. Please check them before sending the transaction.') }}
        </span>
      </div>

      <div class="uk-margin">
        <x-forms.transaction-wallet-select :transaction="$transaction ?? null"/>
      </div>

      <div class="uk-margin">
        <x-forms.transaction-type-select :transaction="$transaction ?? null"/>
      </div>

      <div class="uk-margin">
        <x-forms.date-picker
          fieldName="transaction_date"
          :defaultValue="$transaction['transaction_date'] ?? Auth::user()->previousTransactionDate()"
        />
      </div>

      <a href="{{ route('transaction.view.create.items') }}" class="uk-button uk-button-primary">
        {{ __('Previous step') }}
      </a>
    </x-forms.skeleton>
  </div>
@endsection

