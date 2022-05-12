@isset($steps)
  <ul class="oys-stepper">
    @foreach($steps as $step)
      <li class="oys-step @if($loop->index < $complete)complete @endif">
        <div class="oys-contents">
        <span class="oys-icon">
          @if($loop->index < $complete)
            <span uk-icon="check"></span>
          @else
            {{ $loop->index + 1 }}
          @endif
        </span>
          <span class="oys-text">{{ __($step) }}</span>
        </div>
      </li>
    @endforeach
  </ul>
@endisset
<form
  method="POST"
  action="{{ $action }}"
  enctype="multipart/form-data"
  class="uk-form uk-form-stacked"
>
  @csrf

  {!! $slot !!}

  @if(!isset($final))
    <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
  @endif
  <div class="uk-margin-small-top">
    <button type="button" class="uk-button uk-button-danger cancel-transaction">{{ __('Cancel') }}</button>

    <div class="uk-float-right uk-button-group">
      @if(isset($previousStep))
        <a href="{{ $previousStep }}" class="uk-button">
          <span uk-icon="chevron-left"></span>
          {{ __('Previous step') }}
        </a>
      @endif
      <button type="submit" class="uk-button uk-button-primary">
        {{ __($submitLabel ?? 'Send') }}
        @if(!isset($final))
          <span uk-icon="chevron-right"></span>
        @else
          <span uk-icon="bag"></span>
        @endif

      </button>
    </div>
  </div>
</form>

@push('scripts')
  <script>
    $('.cancel-transaction').click(() => {
      UIkit.modal.confirm('{{ __('All entered data will be lost. Proceed?') }}', {
        labels: {
          ok: '{{ __('yes') }}',
          cancel: '{{ __('no') }}'
        }
      }).then(function () {
        window.location.replace('{{ route('transaction.view.all') }}');
      }, () => {
      });
    });
  </script>
@endpush
