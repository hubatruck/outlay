@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Create a transfer') }}</x-page-title>
  <div class="uk-card-body">
    <form
      method="POST"
      action="{{ route('transfer.data.create') }}"
      enctype="multipart/form-data"
      class="uk-form uk-form-stacked"
    >
      @csrf

      <div class="uk-margin">
        <label for="description" class="uk-form-label">
          {{ __('Description') }}<span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input
            id="description"
            class="uk-input @error('scope') uk-form-danger @enderror"
            name="description"
            type="text"
            value="{{ old('description', '') }}"
            required
          />
        </div>
        @error('description')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <label for="amount" class="uk-form-label">
          {{ __('Amount') }}<span class="uk-text-danger">*</span>
        </label>
        <div class="uk-form-controls">
          <input
            id="amount"
            class="uk-input @error('amount') uk-form-danger @enderror"
            name="amount"
            type="number"
            value="{{ old('amount', 0) }}"
            step="0.01"
            min="0.01"
            max="999999.99"
          />
        </div>
        @error('amount')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <x-forms.wallet-select
          fieldName="from_wallet_id"
          :selectedWalletID="$selected_from_wallet_id"
          :label="__('Source wallet')"
          :wallets="Auth::user()->wallets"
        />
      </div>

      <div class="uk-margin">
        <x-forms.wallet-select
          fieldName="to_wallet_id"
          :selectedWalletID="$selected_to_wallet_id"
          :label="__('Destination wallet')"
          :wallets="\App\Models\Wallet::withTrashed()->get()"
          :addOwnerName="true"
        />
      </div>

      <div class="uk-margin">
        <x-date-picker fieldName="transfer_date"></x-date-picker>
      </div>

      <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
      <div class="uk-margin-small-top">
        <button type="submit" class="uk-button uk-button-primary">
          {{ __('Send') }}
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
