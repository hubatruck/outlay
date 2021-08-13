function () {
    const api = this.api();
    /// column footer search boxes
    api.columns().every(function () {
        const column = this;
        if (!column.dataSrc().match(/date/)) {
            const input = document.createElement("input");
            input.placeholder = column.header().title;
            $(input)
                .addClass("form-control form-control-sm")
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

    $('#dtc-filters').append('<div id="dtc-date-range"><input type="text" placeholder="{{ __('Filter dates') }}" data-input id="{{ $dateRangeID }}"></div>');
    flatpickr.localize(flatpickr.l10ns.hu);
    $('#dtc-date-range').flatpickr({
        mode: 'range',
        altInput: true,
        altInputClass: 'form-control form-control-sm',
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
