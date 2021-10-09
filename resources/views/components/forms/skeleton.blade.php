<form
  method="POST"
  action="{{ $action }}"
  enctype="multipart/form-data"
  class="uk-form uk-form-stacked"
>
  @csrf

  {!! $slot !!}

  <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>

  <div class="uk-margin-small-top">
    <button type="submit" class="uk-button uk-button-primary">
      {{ __($submitLabel ?? 'Send') }}
    </button>
    <x-buttons.cancel-edit
      :url="$cancelURL ?? '#'"
    />
  </div>
</form>
