@php
  $defaultDateRange = defaultChartRangeAsFlatpickrValue()
@endphp

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
    const nonChartClasses = 'uk-text-center uk-margin-xlarge-top uk-margin-xlarge-bottom';
    const loadingTemplate = "<div class=\"" + nonChartClasses + "\"><div uk-spinner></div>&nbsp;{{ __('Loading...') }}</div>";
    let previousRange = "{{ $defaultDateRange }}";

    const rangePicker = $('#chart-date-range').flatpickr({
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

    function resetRange() {
      setRange('{{ $defaultDateRange }}');
    }

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

    $('#reload').click(() => {
      setRange(previousRange);
    });
    $('#7days').click(function () {
      const start = new Date();
      start.setDate(start.getDate() - 7);
      setRange(rangeBeginningWith(start));
    });
    $('#30days').click(function () {
      const start = new Date();
      start.setMonth(start.getMonth() - 1);
      setRange(rangeBeginningWith(start));
    });
    $('#6months').click(function () {
      const start = new Date();
      start.setMonth(start.getMonth() - 6);
      setRange(rangeBeginningWith(start));
    });

    function rangeBeginningWith(rangeStart) {
      const now = new Date();
      const dateLocale = 'en-CA'; // YYYY-MM-DD
      return `${rangeStart.toLocaleDateString(dateLocale)} - ${now.toLocaleDateString(dateLocale)}`;
    }

    function setRange(range) {
      previousRange = range;
      loadCharts(range);
      rangePicker.setDate(range);
    }
  </script>
@endpush
