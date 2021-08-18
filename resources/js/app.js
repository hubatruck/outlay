import './bootstrap';

/// Convert regular date inputs into flatpickr date inputs
$('input[type=date]').each(function () {
    /// ignore DataTables input, they are handled separately
    if (this.id.match('dtc') === null) {
        this.flatpickr({
            altInput: true,
            altInputClass: 'uk-input',
            locale: document.documentElement.lang,
        });
    }
});
