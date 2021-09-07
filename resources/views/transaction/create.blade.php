@extends('layouts.app')

@section('content')
  <x-page-title>{{ __(':action transaction', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <form
      method="POST"
      action="{{ route('transaction.data.create') }}"
      enctype="multipart/form-data"
      class="uk-form uk-form-stacked"
    >
      @csrf
      <ul uk-tab>
        <li class="uk-active"><a href="">{{ __('Items') }}</a></li>
        <li><a href="">{{ __('Payment details') }}</a></li>
      </ul>
      <ul class="uk-switcher">
        <li uk-grid>
          <div class="uk-width-2-3@s">
            <x-forms.text-input
              fieldName="scope"
              :label="__('Scope')"
            />
          </div>

          <div class="uk-width-1-3@s">
            <x-forms.amount-input/>
          </div>

          <div class="uk-text-danger uk-display-block">{{ __('Fields marked with * are required.') }}</div>
        </li>

        <li>
          <div class="uk-margin">
            <x-forms.transaction-wallet-select/>
          </div>

          <div class="uk-margin">
            <x-forms.transaction-type-select/>
          </div>

          <div class="uk-margin">
            <x-forms.date-picker
              fieldName="transaction_date"
              :defaultValue="Auth::user()->previousTransactionDate()"
            />
          </div>

          <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
          <div class="uk-margin-small-top">
            <button type="submit" class="uk-button uk-button-primary">
              {{ __('Send') }}
            </button>
            <x-buttons.cancel-edit
              :url="previousUrlOr(route('transaction.view.all'))"
            />
          </div>
        </li>
      </ul>
    </form>
  </div>
@endsection

