/*! DataTables UIkit 3 integration
 */

/**
 * This is a tech preview of UIKit integration with DataTables.
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'datatables.net'], function ($) {
            return factory($, window, document);
        });
    } else if (typeof exports === 'object') {
        // CommonJS
        module.exports = function (root, $) {
            if (!root) {
                root = window;
            }

            if (!$ || !$.fn.dataTable) {
                // Require DataTables, which attaches to jQuery, including
                // jQuery if needed and have a $ property so we can access the
                // jQuery object that is used
                $ = require('datatables.net')(root, $).$;
            }

            return factory($, root, root.document);
        };
    } else {
        // Browser
        factory(jQuery, window, document);
    }
}(function ($, window, document) {
    'use strict';
    const DataTable = $.fn.dataTable;


    /* Set the defaults for DataTables initialisation */
    $.extend(true, DataTable.defaults, {
        dom:
            "<'row uk-grid'<'uk-width-1-2'l><'uk-width-1-2'f>>" +
            "<'row uk-grid dt-merge-grid'<'uk-width-1-1'rt>>" +
            "<'row uk-grid dt-merge-grid'<'uk-width-2-5'i><'uk-width-3-5'p>>",
        renderer: 'uikit'
    });

    /* Default class modification */
    $.extend(DataTable.ext.classes, {
        sWrapper: "dataTables_wrapper uk-form dt-uikit",
        sFilterInput: "uk-input",
        sLengthSelect: "uk-form-small uk-select uk-form-width-xsmall",
        sProcessing: "dataTables_processing uk-label uk-label-warning uk-padding-small uk-position-center"
    });

    /* This wrapper is needed for the dropdowns to work correctly */
    $.extend(DataTable.Buttons.defaults.dom, {
        container: {
            tag: 'div',
            className: 'uk-grid'
        },
        buttonContainer: {
            tag: 'div',
            className: 'dt-button-wrapper uk-width-1-2@s uk-width-auto@m'
        }
    })

    /* UIkit paging button renderer */
    DataTable.ext.renderer.pageButton.uikit = function (settings, host, idx, buttons, page, pages) {
        const api = new DataTable.Api(settings);
        const classes = settings.oClasses;
        const lang = settings.oLanguage.oPaginate;
        const aria = settings.oLanguage.oAria.paginate || {};
        let btnDisplay, btnClass, counter = 0;

        const attach = function (container, buttons) {
            let i, ien, node, button;
            const clickHandler = function (e) {
                e.preventDefault();
                if (!$(e.currentTarget).hasClass('disabled') && api.page() !== e.data.action) {
                    api.page(e.data.action).draw('page');
                }
            };

            for (i = 0, ien = buttons.length; i < ien; i++) {
                button = buttons[i];

                if (Array.isArray(button)) {
                    attach(container, button);
                } else {
                    btnDisplay = '';
                    btnClass = '';

                    switch (button) {
                        case 'ellipsis':
                            btnDisplay = '<span uk-icon="more"></span>';
                            btnClass = 'uk-disabled disabled';
                            break;

                        case 'first':
                            btnDisplay = '<span uk-icon="chevron-double-left"></span> ' + lang.sFirst;
                            btnClass = (page > 0 ?
                                '' : ' uk-disabled disabled');
                            break;

                        case 'previous':
                            btnDisplay = '<span uk-icon="chevron-left"></span> ' + lang.sPrevious;
                            btnClass = (page > 0 ?
                                '' : 'uk-disabled disabled');
                            break;

                        case 'next':
                            btnDisplay = lang.sNext + ' <span uk-icon="chevron-right"></span>';
                            btnClass = (page < pages - 1 ?
                                '' : 'uk-disabled disabled');
                            break;

                        case 'last':
                            btnDisplay = lang.sLast + '<span uk-icon="chevron-double-right"></span>';
                            btnClass = (page < pages - 1 ?
                                '' : ' uk-disabled disabled');
                            break;

                        default:
                            btnDisplay = button + 1;
                            btnClass = page === button ?
                                'uk-active' : '';
                            break;
                    }

                    if (btnDisplay) {
                        node = $('<li>', {
                            'class': classes.sPageButton + ' ' + btnClass,
                            'id': idx === 0 && typeof button === 'string' ?
                                settings.sTableId + '_' + button :
                                null
                        })
                            .append($((-1 !== btnClass.indexOf('disabled') || -1 !== btnClass.indexOf('active')) ? '<span>' : '<a>', {
                                    'href': '#',
                                    'aria-controls': settings.sTableId,
                                    'aria-label': aria[button],
                                    'data-dt-idx': counter,
                                    'tabindex': settings.iTabIndex
                                })
                                    .html(btnDisplay)
                            )
                            .appendTo(container);

                        settings.oApi._fnBindAction(
                            node, {action: button}, clickHandler
                        );

                        counter++;
                    }
                }
            }
        };

        // IE9 throws an 'unknown error' if document.activeElement is used
        // inside an iframe or frame.
        let activeEl;

        try {
            // Because this approach is destroying and recreating the paging
            // elements, focus is lost on the select button which is bad for
            // accessibility. So we want to restore focus once the draw has
            // completed
            activeEl = $(host).find(document.activeElement).data('dt-idx');
        } catch (e) {
        }

        attach(
            $(host).empty().html('<ul class="uk-pagination uk-pagination-right uk-flex-center"/>').children('ul'),
            buttons
        );

        if (activeEl) {
            $(host).find('[data-dt-idx=' + activeEl + ']').trigger('focus');
        }
    };

    return DataTable;
}));
