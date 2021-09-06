@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Create a transfer') }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="route('transfer.data.create')"
      :cancelURL="previousUrlOr(route('transaction.view.all'))"
    >
      <div class="uk-margin">
        <x-forms.text-input
          fieldName="description"
          :label="__('Description')"
        />
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
          :wallets="\App\Models\Wallet::everyPublicWallet()"
          :addOwnerName="true"
        />
      </div>

      <div class="uk-margin">
        <x-forms.date-picker fieldName="transfer_date"/>
      </div>
    </x-forms.skeleton>
  </div>
@endsection
