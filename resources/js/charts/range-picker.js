/**
 * Classes for non-chart components (ie. loading, errors)
 * @type {string}
 */
const nonChartClasses = 'uk-text-center uk-margin-xlarge-top uk-margin-xlarge-bottom';

/**
 * HTML template for the loading component
 * @type {string}
 */
const loadingTemplate = "<div class=\"" + nonChartClasses + "\"><div uk-spinner></div></div>";

/**
 * Previously selected and loaded date range
 * @type {string}
 */
let previousRange = rpConfig.defaultDateRange;

/**
 * Range picker component
 */
const rangePicker = $('#chart-date-range').flatpickr({
    mode: 'range',
    altInput: true,
    locale: rpConfig.locale,
    onClose: function (selectedDates, dateStr) {
        if (previousRange !== dateStr) {
            previousRange = dateStr;
            loadCharts(dateStr);
        }
    },
    onReady: function () {
        loadCharts(rpConfig.defaultDateRange);
    },
    defaultDate: rpConfig.defaultDateRange
});

/**
 * Reset range to default value
 */
function resetRange() {
    setRange(rpConfig.defaultDateRange);
}

/**
 * Load chart page
 *
 * @param range Date range for the charts
 */
function loadCharts(range) {
    let scrollLocation = document.documentElement.scrollTop;
    setChartContainerContents(loadingTemplate, () => {
        $.ajax({
            url: rpConfig.chartURL,
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

/**
 * Set the chart container's contents
 *
 * @param content Content to put into the container
 * @param callback Called after content is set in the container
 * @param duration Duration of the fade in/out animation in ms
 */
function setChartContainerContents(content, callback = null, duration = 500) {
    const container = $(rpConfig.chartContainer);
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

/**
 * Generates a range string with the given starting date and today as ending
 *
 * @param rangeStart
 * @returns {string}
 */
function rangeBeginningWith(rangeStart) {
    const now = new Date();
    const dateLocale = 'en-CA'; // YYYY-MM-DD
    return `${rangeStart.toLocaleDateString(dateLocale)} - ${now.toLocaleDateString(dateLocale)}`;
}

/**
 * Sets the range in the picker, and loads the charts
 *
 * @param range preferably in "YYYY-MM-DD - YYYY-MM-DD" format
 */
function setRange(range) {
    previousRange = range;
    loadCharts(range);
    rangePicker.setDate(range);
}
