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
    const nonChartClasses = 'uk-text-center uk-margin-xlarge-top uk-margin-xlarge-bottom';
    const loadingTemplate = "<div class=\"" + nonChartClasses + "\"><div uk-spinner></div>&nbsp;{{ __('Loading...') }}</div>";
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
        loadCharts("{{ $defaultDateRange }}");
      },
      defaultDate: "{{ $defaultDateRange }}"
    });

    function loadCharts(range) {
      let scrollLocation = document.documentElement.scrollTop;
      setChartContainerContents(loadingTemplate, () => {
        const request = $.ajax({
          url: "{{ route('wallet.view.charts', ['id' => $walletID]) }}",
          data: {range: range},
          success: (data) => {
            setChartContainerContents(data, () => {
              $('html, body').animate({
                scrollTop: scrollLocation,
              }, 300);
            }, 100);
          },
          error: () => {
            setChartContainerContents("<div class=\"" + nonChartClasses + "\">{{ __('Uh-oh, something went wrong! Please try again later.') }}</div>",);
          }
        });
      });
    }

    function setChartContainerContents(content, callback, duration = 500) {
      const container = $('{{ $chartContainer }}');
      container.fadeOut(duration, () => {
        container.html(content);
        container.fadeIn(duration, callback);
      });
    }
  </script>
@endpush
