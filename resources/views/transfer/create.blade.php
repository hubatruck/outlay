@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Create a transfer') }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="route('transfer.data.create')"
      :cancelURL="previousUrlOr(route('transaction.view.all'))"
    >
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
        <x-forms.amount-input/>
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
        <x-forms.date-picker fieldName="transfer_date"></x-forms.date-picker>
      </div>
    </x-forms.skeleton>
  </div>
@endsection
