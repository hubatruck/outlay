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
      <ul uk-tab>
        <li class="uk-active"><a href="">{{ __('Items') }}</a></li>
        <li><a href="">{{ __('Payment details') }}</a></li>
      </ul>
      <ul class="uk-switcher">
        <li uk-grid>
          <div class="uk-width-1-1" id="transaction-items">
            <div class="uk-grid">
              <div class="uk-width-2-3@s">
                <x-forms.text-input
                  fieldName="scope[]"
                  :label="__('Scope')"
                />
              </div>

              <div class="uk-width-1-3@s uk-inline">
                <x-forms.amount-input asArray="true"/>
                <span class="remove-row"></span>
              </div>
            </div>
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
    $('#new-row-button').click(() => {
      const newItem = container.children().last().clone();
      const closeIcon = newItem.children().children('span')[0];
      if (closeIcon.innerHTML === undefined || closeIcon.innerHTML === '') {
        UIkit.icon(closeIcon, {icon: 'trash'});
        closeIcon.innerHTML += "{{ __('Delete this row') }}";
      }
      newItem.appendTo(container);
    });

    $(container).on("click", ".remove-row", function (e) { //user click on remove text
      e.preventDefault();
      $(this).parent('div').parent('div').remove();
    })
  </script>
@endpush
