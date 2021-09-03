<div class="uk-padding-small">
  <div class="uk-button-group uk-width-expand uk-margin-small">
    <button id="reload" class="uk-button uk-button-default">{{ __('Reload data') }}</button>
    <button id="7days" class="uk-button uk-button-default">{{ __('Last 7 days') }}</button>
    <button id="30days" class="uk-button uk-button-default">{{ __('Last 30 days') }}</button>
    <button id="6months" class="uk-button uk-button-default">{{ __('Last 6 months') }}</button>
  </div>
  <label
    class="uk-form-label uk-text-bold uk-margin-small-right"
    for="chart-date-range"
  > {{ __('Chart date interval') }}: </label>
  <div class="uk-inline">
    <button
      class="uk-form-icon uk-form-icon-flip"
      uk-icon="trash"
      onclick="resetRange()"
      uk-tooltip="{{ __('Reset range') }}"
    ></button>
    <input
      type="date"
      class="uk-input uk-form-width-large" id="chart-date-range"
      placeholder="{{ __('Show charts between...') }}"
    >
  </div>
</div>

@push('scripts')
  <script>
    const rpConfig = {
      chartURL: "{{ route('wallet.view.charts', ['id' => $walletID]) }}",
      chartContainer: "{{ $chartContainer }}",
      defaultDateRange: "{{ defaultChartRangeAsFlatpickrValue() }}",
      locale: "{{ config('app.locale') }}",
      errorMessage: "{{ __('Uh-oh, something went wrong! Please try again later.') }}"
    }
  </script>
  <script src="{{ mix('js/charts.bundle.min.js') }}"></script>
@endpush
