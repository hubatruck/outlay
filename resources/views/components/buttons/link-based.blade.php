<a href="{{ $url ?? '#' }}" class="uk-button uk-button-{{ $type ?? 'default' }}">
  @if (isset($icon))
    <span uk-icon="{{ $icon }}"></span>
  @endif
  {{ __($label ?? 'UNDEFINED') }}
</a>
