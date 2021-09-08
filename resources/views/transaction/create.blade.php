@extends('layouts.app')

@section('content')
  <x-page-title>{{ __(':action transaction', ['action' => __('Create')]) }}</x-page-title>
  <div class="uk-card-body">
    <form
      method="POST"
      action="{{ route('transaction.data.test-create') }}"
      enctype="multipart/form-data"
      class="uk-form uk-form-stacked"
    >
      @csrf

      <div
        id="has-errors"
        class="uk-flex uk-width-1-1"
        @unless ($errors->any())
        style="display: none"
        @endunless
      >
        <span class="uk-alert uk-alert-danger uk-text-bold uk-width-1-1">
          {{ __('Some input fields are invalid. Please check them before submitting.') }}
        </span>
      </div>
      <ul class="ukc-tab-header" uk-tab="animation:uk-animation-scale-down, uk-animation-scale-up">
        <li class="uk-active"><a href="">{{ __('Items') }}</a></li>
        <li><a href="">{{ __('Payment details') }}</a></li>
      </ul>
      <ul class="uk-switcher">
        <li uk-grid>
          <div class="uk-width-1-1" id="transaction-items">
            @forelse(old('scope') ?? [] as $stepper)
              <x-forms.transaction-item :index="$loop->index"/>
            @empty
              <x-forms.transaction-item/>
            @endforelse
          </div>
          <div class="uk-text-danger uk-width-1-1">{{ __('Fields marked with * are required.') }}</div>
          <div class="uk-width-1-1">
            <a href="#" class="uk-button uk-button-primary" id="new-row-button">
              <span uk-icon="plus"></span>
              {{ __('Add a row') }}
            </a>
          </div>
        </li>

        <li>
          <div class="uk-margin">
            <x-forms.transaction-wallet-select/>
          </div>

          <div class="uk-margin">
            <x-forms.transaction-type-select/>
          </div>

          <div class="uk-margin">
            <x-forms.date-picker
              fieldName="transaction_date"
              :defaultValue="Auth::user()->previousTransactionDate()"
            />
          </div>

          <div class="uk-text-danger">{{ __('Fields marked with * are required.') }}</div>
          <div class="uk-margin-small-top">
            <button type="submit" class="uk-button uk-button-primary">
              {{ __('Send') }}
            </button>
            <x-buttons.cancel-edit
              :url="previousUrlOr(route('transaction.view.all'))"
            />
          </div>
        </li>
      </ul>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    const container = $('#transaction-items');
    $('#new-row-button').click((event) => {
      event.preventDefault();

      const newItem = container.children().last().clone();
      const closeIcon = newItem.children().children('span').last();
      if (closeIcon.html() === '') {
        UIkit.icon(closeIcon, {icon: 'trash'});
        closeIcon.html("{{ __('Delete this row') }}");
      }
      newItem.appendTo(container);
    });

    $(container).on("click", ".remove-row", function (e) { //user click on remove text
      e.preventDefault();
      $(this).parent('div').parent('div').remove();
    });

    $(':submit').click((event) => {
      event.preventDefault();
      const inputs = $('.transaction-item').find('input');

      let hasErrors = false;
      inputs.each((index, item) => {
        if (!item.checkValidity()) {
          item.classList += ' uk-form-danger uk-alert-danger';
          hasErrors = true;
        }
      });

      if (hasErrors) {
        $('#has-errors').show();
        UIkit.tab('.ukc-tab-header').show(0);
        return;
      }

      $('form').submit();
    });
  </script>
@endpush
