@php
  $defaultDateRange = date('Y-m-01').' - '.currentDayOfTheMonth()
@endphp

<div class="uk-padding-small uk-flex uk-flex-middle">
  <label class="uk-form-label uk-margin-small-right" for="chart-date-range">{{ __('Chart date interval') }}:</label>
  <input
    type="date"
    class="uk-input uk-form-width-large" id="chart-date-range"
    placeholder="{{ __('Show charts between...') }}"
  >
</div>

@push('scripts')
  <script>
    let previousRange = "{{ $defaultDateRange }}";

    $('#chart-date-range').flatpickr({
      mode: 'range',
      altInput: true,
      locale: "{{ config('app.locale') }}",
      onClose: function (selectedDates, dateStr) {
        if (previousRange !== dateStr) {
          previousRange = dateStr;
          loadCharts(dateStr);
        }
      },
      onReady: function () {
        loadCharts("{{ date('Y-m-01').' - '.currentDayOfTheMonth() }}");
      },
    });

    function loadCharts(range) {
      const container = $('{{ $chartContainer }}');
      const request = $.ajax({
        url: "{{ route('wallet.view.charts', ['id' => $walletID]) }}",
        data: {range: range},
      });
      request.done(function (data) {
        container.html(data);
      });
    }
  </script>
@endpush
