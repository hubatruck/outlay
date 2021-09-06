function () {
    const api = this.api();
    let previousRange = '';
    const pickerTemplate =
        '{{ __('Filter dates') }}:' +
        '<div id="dtc-date-range" class="uk-inline uk-width-1-1 uk-form-controls">' +
        '   <a id="dtc-reset" class="uk-form-icon uk-form-icon-flip" uk-icon="home" uk-tooltip="{{ __('Reset range') }}" data-clear></a>' +
        '   <input id="{{ $dateRangeID }}" type="text" placeholder="{{ __('Select the date range to be shown') }}" data-input>' +
        '</div>';

    /// column footer search boxes
    api.columns().every(function () {
        const column = this;
        if (!column.dataSrc().match(/date/) && !column.dataSrc().match(/action/)) {
            const input = document.createElement("input");
            input.placeholder = column.header().title;
            input.type = 'text';
            $(input)
                .addClass("uk-input uk-form-small")
                .appendTo($(column.footer()).empty())
                .on('change', function () {
                    column.search($(this).val(), false, false, true).draw();
                });
        }
    });
    /// reset footer search boxes on 'reset' button
    //        api.on('stateLoaded.dt', (e, settings,data)=>{
    //            api.columns().every(function (colIdx) {
    //                  input.value = this.state().columns[column.index()].search.search;
    //                var colSearch = this.state().columns[colIdx].search;
    //                $('input', this.columns(colIdx).footer()).val(colSearch.search);
    //            });
    //        });

    $('#dtc-filters').append(pickerTemplate);
    flatpickr.localize(flatpickr.l10ns.hu);
    $('#dtc-date-range').flatpickr({
        mode: 'range',
        altInput: true,
        altInputClass: 'uk-input uk-form-small',
        wrap: true,
        locale: {
            firstDayOfWeek: 1,
        },
        onClose: function (selectedDates, dateStr) {
            if (previousRange !== dateStr) {
                previousRange = dateStr;
                api.draw()
            }
        },
        locale: "{{ config('app.locale') }}"
    });

    $('#dtc-reset').click(() => {
        api.draw();
    });
}
