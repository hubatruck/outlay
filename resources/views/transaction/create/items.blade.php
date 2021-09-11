@extends('layouts.app')

@section('content')
  <x-page-title>{{ __('Transaction creator') }} - {{ __('Items') }}</x-page-title>
  <div class="uk-card-body">
    <x-forms.skeleton
      :action="route('transaction.data.create.items')"
      submitLabel="Next step"
      :cancelURL="route('transaction.view.all')"
    >
      <div
        id="has-errors"
        class="uk-flex uk-width-1-1"
        @unless ($errors->any())
        style="display: none"
        @endunless
      >
        <span class="uk-alert uk-alert-danger uk-text-bold uk-width-1-1">
          {{ __('Some fields are invalid. Please check them before going to the next step.') }}
        </span>
      </div>

      <div class="uk-width-1-1" id="transaction-items">
        @forelse($transaction['scope'] ?? [] as $stepper)
          <x-forms.transaction-item :index="$loop->index" :transaction="$transaction"/>
        @empty
          <x-forms.transaction-item/>
        @endforelse
      </div>
      <div class="uk-width-1-1 uk-margin-small-top">
        <a href="#" class="uk-button uk-button-default" id="new-row-button">
          <span uk-icon="plus"></span>
          {{ __('Add a row') }}
        </a>
      </div>
    </x-forms.skeleton>
  </div>
@endsection

@push('scripts')
  <script>
    const container = $('#transaction-items');
    $('#new-row-button').click((event) => {
      event.preventDefault();
      const newItem = container.children().last().clone();
      const inputs = newItem.find('input');

      initInputElement(inputs.first());
      initInputElement(inputs.last());
      initRemoveButton(newItem.find('span').last());

      newItem.appendTo(container);
      inputs.first().focus();
    });

    $(container).on("click", ".remove-row", function (e) { /// user click on remove text
      e.preventDefault();
      $(this).parent('div').parent('div').remove();
    });

    function initInputElement(element) {
      element.parent().siblings('.uk-text-danger').remove(); /// error field
      element.removeClass('uk-form-danger'); /// error styling
      element.val('');
    }

    function initRemoveButton(element) {
      if (element.html() === '') {
        UIkit.icon(element, {icon: 'trash'});
        element.html("{{ __('Delete this row') }}");
      }
    }
  </script>
@endpush
