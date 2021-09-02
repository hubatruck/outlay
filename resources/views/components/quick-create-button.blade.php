<a
  class="uk-button uk-button-primary @if($wallet->deleted_at)uk-link-muted @endif"
  href="{{ $wallet->deleted_at ? '#' : route($targetType . '.view.create', ['wallet_id' => $wallet->id]) }}"
  @if($wallet->deleted_at)
  uk-tooltip="{{ __('This wallet cannot be used for new :target creation until reactivated.', ['target' => __($targetType)]) }}"
  @endif
>
  <span class="uk-margin-small" uk-icon="plus"></span>
  {{ __('Add ' . $targetType) }}
</a>
