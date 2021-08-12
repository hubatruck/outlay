function a() {
var api = this.api();
var resizing = false;
/// column footer search boxes
api.columns().every(function (colIdx) {
var column = this;
var input = document.createElement("input");
input.classList = 'form-control form-control-sm';
input.placeholder = column.header().title;
$(input).appendTo($(column.footer()).empty())
.on('change', function () {
column.search($(this).val(), false, false, true).draw();
});
});

/// reset footer search boxes on 'reset' button
//        api.on('stateLoaded.dt', (e, settings,data)=>{
//            api.columns().every(function (colIdx) {
//                  input.value = this.state().columns[column.index()].search.search;
//                var colSearch = this.state().columns[colIdx].search;
//                $('input', this.columns(colIdx).footer()).val(colSearch.search);
//            });
//        });
}
