@extends('layouts.app')

@section('content')
  <x-page-title>{{ isset($wallet) ? __(':action wallet', ['action'=> __('Edit')]) : __(':action wallet', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <form
      method="POST"
      action="{{ isset($wallet) ? route('wallet.data.update', ['id' => $wallet->id]) : route('wallet.data.create') }}"
      enctype="multipart/form-data"
      class="uk-form-stacked"
    >
      @csrf

      <div class="uk-margin">
        <label for="name" class="uk-form-label">{{ __('Name') }}<span class="uk-text-danger">*</span></label>
        <div class="uk-form controls">
          <input
            id="name"
            class="uk-input @error('name')uk-form-danger @enderror"
            name="name"
            type="text"
            value="{{ old('name', isset($wallet) ? $wallet->name : '') }}"
            required
          />
        </div>
        @error('name')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="uk-margin">
        <label for="notes" class="uk-form-label">{{ __('Notes') }}</label>
        <div class="form-controls">
        <textarea
          id="notes"
          class="uk-textarea @error('notes')uk-form-danger @enderror"
          name="notes"
          placeholder="{{ __('This field is optional.') }}"
        >{{ old('notes', isset($wallet) ? $wallet->notes : '') }}</textarea>
        </div>
        @error('notes')
        <span class="uk-text-danger uk-text-small">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
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

      <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
      <div class="uk-margin-small-top">
        <button type="submit" class="uk-button uk-button-primary">
          {{ isset($wallet) ? __('Save') : __('Create') }}
        </button>
        <a
          type="submit"
          href="{{ previousUrlOr(isset($wallet) ? route('wallet.view.details', ['id' => $wallet->id]) : route('wallet.view.all')) }}"
          class="uk-button uk-button-danger"
        >
          {{ __('Cancel') }}
        </a>
      </div>
    </form>
  </div>
@endsection
