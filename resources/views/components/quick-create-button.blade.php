<div class="uk-inline {{ $class ?? '' }}">
  <a
    class="uk-button @unless(isset($notPrimary))uk-button-primary @endunless @if($wallet->deleted_at)uk-link-muted @endif"
    href="{{ $wallet->deleted_at ? '#' : route($targetType . '.view.create', [$destinationParam ?? 'wallet_id' => $wallet->id]) }}"
    @if($wallet->deleted_at)
    uk-tooltip="{{ __('This wallet cannot be used for new :target creation until reactivated.', ['target' => __($targetType)]) }}"
    @endif
  >
    <span class="uk-margin-small" uk-icon="{{  $icon ?? 'plus' }}"></span>
    {{ $label ?? (__('Add ' . $targetType)) }}
  </a>
  @if (isset($dropdownContent) && $wallet->deleted_at === null)
    <div uk-dropdown class="uk-padding-remove uk-child-width-expand">
      {!! $dropdownContent !!}
    </div>
  @endif
</div>
