@extends('layouts.app')

@section('content')
  <x-page-title>{{ isset($transaction) ? __(':action transaction', ['action'=> __('Edit')]) : __(':action transaction', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <form
      method="POST"
      action="{{ isset($transaction) ? route('transaction.data.update', ['id' => $transaction->id]) : route('transaction.data.create') }}"
      enctype="multipart/form-data"
      class="uk-form uk-form-stacked"
    >
      @csrf

      <div class="uk-margin">
        <label for="scope" class="uk-form-label">
          {{ __('Scope') }}<span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input
            id="scope"
            class="uk-input @error('scope')uk-form-danger @enderror"
            name="scope"
            type="text"
            value="{{ old('scope', $transaction->scope ?? '') }}"
            required
          />
        </div>
        @error('scope')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <label for="amount" class="uk-form-label">{{ __('Amount') }}</label>
        <div class="uk-form-controls">
          <input
            id="amount"
            class="uk-input @error('amount')uk-form-danger @enderror"
            name="amount"
            type="number"
            value="{{ old('amount', $transaction->amount ?? 0) }}"
            step="0.01"
          />
        </div>
        @error('amount')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <label for="wallet_id" class="uk-form-label">
          {{ __('Wallet') }}<span class="uk-text-danger">*</span></label>
        <div class="uk-form-controls">
          <select
            id="wallet_id"
            class="uk-select @error('wallet_id')uk-form-danger @enderror"
            name="wallet_id"
            @if (Auth::user()->hasAnyActiveWallet())
            required
            @else
            disabled
            @endif
          >
            <option @if(!isset($transaction)) selected @endif disabled hidden value="">
              {{ __('Select...') }}
            </option>
            @foreach(Auth::user()->wallets as $wallet)
              @if ($wallet->deleted_at === null
                  || (isset($transaction) && $transaction->wallet_id === $wallet->id))
                <option
                  value="{{ $wallet->id }}"
                  @if((isset($transaction) && $wallet->id === $transaction->wallet_id)
                      || ($selected_wallet_id ?? '') === (string) $wallet->id
                      || (old('wallet_id') && (string) $wallet->id === old('wallet_id')))
                  selected
                  @endif
                  @if ($wallet->deleted_at !== null)
                  disabled
                  @endif
                >
                  {{ $wallet->name }}
                </option>
              @endif
            @endforeach
          </select>
          @if (!Auth::user()->hasAnyActiveWallet())
            <span class="uk-alert-primary uk-padding-remove">
              {{ __('No active wallets; this field cannot be changed.') }}
            </span>
          @endif
        </div>
        @error('wallet_id')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <label for="transaction_type_id" class="uk-form-label">
          {{ __('Type') }}<span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <select
            id="transaction_type_id"
            class="uk-select @error('transaction_type_id') uk-form-danger @enderror"
            name="transaction_type_id"
            required
          >
            <option @if(!isset($transaction)) selected @endif disabled hidden value="">
              {{ __('Select...') }}
            </option>
            @foreach(\App\Models\TransactionType::all() as $tt)
              <option
                value="{{ $tt->id }}"
                @if(isset($transaction) && $transaction->transactionType->id === $tt->id)
                selected
                @endif
                @if(old('transaction_type_id') && (string)$tt->id === old('transaction_type_id'))
                selected
                @endif
              >{{ __($tt->name) }}</option>
            @endforeach
          </select>
        </div>
        @error('transaction_type_id')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <x-date-picker
          fieldName="transaction_date"
          :defaultValue="$transaction->transaction_date ?? Auth::user()->previousTransactionDate()"
        ></x-date-picker>
      </div>

      <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
      <div class="uk-margin-small-top">
        <button type="submit" class="uk-button uk-button-primary">
          {{ isset($transaction) ? __('Save') : __('Create') }}
        </button>
        <a type="submit"
           href="{{ previousUrlOr(route('transaction.view.all')) }}"
           class="uk-button uk-button-danger"
        >
          {{ __('Cancel') }}
        </a>
      </div>
    </form>
  </div>

@endsection
