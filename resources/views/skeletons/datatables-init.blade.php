function () {
    const api = this.api();
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

    $('#dtc-filters').append('{{ __('Filter dates') }}:<div id="dtc-date-range"> <input type="text" placeholder="{{ __('Select the date range to be shown') }}" data-input id="{{ $dateRangeID }}"></div>');
    flatpickr.localize(flatpickr.l10ns.hu);
    $('#dtc-date-range').flatpickr({
        mode: 'range',
        altInput: true,
        altInputClass: 'uk-input uk-form-small',
        wrap: true,
        locale: {
            firstDayOfWeek: 1,
        },
        onChange: function () {
            api.draw();
        },
        locale: "{{ config('app.locale') }}"
    });
}
