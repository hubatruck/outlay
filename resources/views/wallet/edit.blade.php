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
        <div class="uk-form-controls uk-form-controls-text">
          <label>
            <input
              id="is_public"
              class="uk-checkbox"
              name="is_public"
              type="checkbox"
              {{ old('is_public', isset($wallet) && $wallet->is_public ? 'checked' : '') }}
            >
            {{ __('Make wallet publicly available') }}
            <span
              uk-icon="question"
              uk-tooltip="{{ __('If checked, other users can send funds to this wallet.') }}"
            />
          </label>
        </div>
      </div>
    </x-forms.skeleton>
  </div>
@endsection
