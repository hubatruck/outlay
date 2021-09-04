@extends('layouts.app')

@section('content')
  <x-page-title>{{ isset($transaction) ? __(':action transaction', ['action'=> __('Edit')]) : __(':action transaction', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="isset($transaction) ? route('transaction.data.update', ['id' => $transaction->id]) : route('transaction.data.create')"
      :cancelURL="previousUrlOr(route('transaction.view.all'))"
    >
      <div class="uk-margin">
        <x-forms.text-input
          fieldName="scope"
          :label="__('Scope')"
          :value="$transaction->scope ?? ''"
        />
      </div>

      <div class="uk-margin">
        <x-forms.amount-input :value="$transaction->amount ?? 0"/>
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
          :defaultValue="$transaction->transaction_date ?? Auth::user()->previousTransactionDate()"
        />
      </div>
    </x-forms.skeleton>
  </div>

@endsection
