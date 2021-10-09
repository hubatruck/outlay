<div class="uk-margin-small-bottom">
  <x-alert
    type="danger"
    :content="__('Please provide :thing before proceeding further.', ['thing' => __($provideThis)])"
  />
  {{ __('Click the "Change" button below this message to get taken to that wizard page.') }}
</div>
