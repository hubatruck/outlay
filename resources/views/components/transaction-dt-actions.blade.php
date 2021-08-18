<button class="uk-button uk-button-small uk-button-default" type="button">
  </span>{{ __('Actions') }}
</button>
<div class="uk-padding-small" uk-dropdown="mode: click">
  <ul class="uk-nav uk-dropdown-nav">
    <li>
      <a class="uk-button uk-button-small uk-text-success" href="{{ $editURL }}">
        <span uk-icon="pencil"></span>{{ __('Edit') }}
      </a>
    </li>
    <li>
      <a class="uk-button uk-button-small uk-text-danger" href="{{ $deleteURL }}">
        <span uk-icon="trash"></span>{{ __('Delete') }}
      </a>
    </li>
  </ul>
</div>
