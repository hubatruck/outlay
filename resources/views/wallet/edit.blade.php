@extends('layouts.app')

@section('content')
  <x-page-title>{{ isset($wallet) ? __(':action wallet', ['action'=> __('Edit')]) : __(':action wallet', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="isset($wallet) ? route('wallet.data.update', ['id' => $wallet->id]) : route('wallet.data.create')"
      :cancelURL="previousUrlOr(isset($wallet) ? route('wallet.view.details', ['id' => $wallet->id]) : route('wallet.view.all'))"
    >
      <div class="uk-margin">
        <x-forms.text-input
          fieldName="name"
          :label="__('Name')"
          :value="$wallet->name ?? ''"
        />
      </div>

      <div class="uk-margin">
        <x-forms.textarea
          fieldName="notes"
          :label="__('Notes')"
          :value="$wallet->notes ?? ''"
        />
      </div>

      <div class="uk-margin">
        <label for="balance" class="uk-form-label">{{ __('Balance') }}</label>
        <div class="uk-form controls">
          <input
            id="balance"
            class="uk-input @error('balance')uk-form-danger @enderror"
            name="balance"
            type="number"
            value="{{ old('balance', isset($wallet) ? $wallet->balance : 0) }}"
            step="0.01"
          />
        </div>
        @error('balance')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <div class="uk-form-controls uk-form-controls-text">
          <label>
            <input
              id="is_card"
              class="uk-checkbox"
              name="is_card"
              type="checkbox"
              {{ old('is_card', isset($wallet) && $wallet->is_card ? 'checked' : '') }}
            >
            {{ __('Wallet represents credit card') }}
          </label>
        </div>
      </div>
    </x-forms.skeleton>
  </div>
@endsection
