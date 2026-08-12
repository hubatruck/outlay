(function ($, DataTable, UIkit) {
    "use strict";

    // Source: https://github.com/yajra/laravel-datatables-vite/blob/main/js/buttons/helper.js
    const _buildUrl = function (dt, action) {
        const url = dt.ajax.url() || '';
        const params = dt.ajax.params();
        params.action = action;
        params._token = $('meta[name="csrf-token"]').attr('content');

        return url + '?' + $.param(params);
    };

    const _handleCollection = function (event, dt, $button, config) {
        event.stopPropagation();

        let $colElement = $button.data("collection-instance");
        if ($colElement === undefined) {
            const $inline = $('<div class="uk-inline uk-inline-dynamic uk-width-1"></div>');
            $button.wrap($inline);

            const $colElement = $('<div class="uk-card uk-card-body"></div>');
            $colElement.append(config._collection[0]);
            $button.parent().append($colElement);
            UIkit.update();
            $button.data('collection-instance', $colElement[0]);
        }
        $($colElement).toggleClass("uk-hidden");
    };

    const baseButtonClass = 'uk-button uk-button-default uk-width-1-1@s';
    const visibleToggleColClass = 'uk-button-primary';

    DataTable.ext.buttons.export = {
        name: 'export',
        extend: 'collection',
        className: 'buttons-export ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="cloud-download"></span> ' + dt.i18n('buttons.export', 'Export') + '&nbsp;<span class="caret"/>';
        },
        buttons: [
            'pdf',
            'excel',
            'csv',
        ]
    };

    DataTable.ext.buttons.excel = {
        name: 'excel',
        className: 'buttons-excel ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="database"></span> ' + dt.i18n('buttons.excel', 'Excel');
        },
        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'excel'), '_blank');
        }
    };

    DataTable.ext.buttons.csv = {
        name: 'csv',
        className: 'buttons-csv ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="list"></span> ' + dt.i18n('buttons.csv', 'CSV');
        },
        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'csv'), '_blank');
        }
    };

    DataTable.ext.buttons.pdf = {
        name: 'pdf',
        className: 'buttons-pdf ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="file-pdf"></span> ' + dt.i18n('buttons.pdf', 'PDF');
        },
        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'pdf'), '_blank');
        }
    };

    DataTable.ext.buttons.print = {
        name: 'print',
        className: 'buttons-print ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="print"></span>' + dt.i18n('buttons.print', 'Print');
        },
        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'print'), '_blank');
        }
    };

    // Source: https://github.com/yajra/laravel-datatables-vite/blob/main/js/buttons/reset.js
    DataTable.ext.buttons.reset = {
        name: 'reset',
        className: 'buttons-reset ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.reset', 'Reset');
        },
        action: function (e, dt, button, config) {
            $('.dataTable').find(':input').each(function () {
                $(this).val('');
            }).each(function (e) {
                let val = DataTable.util.escapeRegex($(this).val());
                dt.table().column($(this).closest('th').index()).search(val ? val : '', false, true);
            });
            dt.search('').draw();
        }
    };

    // Source: https://github.com/yajra/laravel-datatables-vite/blob/main/js/buttons/reload.js
    DataTable.ext.buttons.reload = {
        name: 'reload',
        className: 'buttons-reload ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.reload', 'Reload');
        },
        action: function (e, dt, button, config) {
            dt.draw(false);
        },
        init: function (dt, node, config) {
            let instance = this;
            dt.on('processing.dt', (e, settings, processing) => {
                let button = $(node);

                if (processing) {
                    button.html('<i class="spinner-border spinner-border-sm" role="status">\n' +
                        '  <span class="visually-hidden">' + dt.i18n('status.loading', 'Loading...') + '</span>\n' +
                        '</i>');
                } else {
                    button.html(config.text);
                }

                button.attr('disabled', processing);
            });
        }
    };

    DataTable.ext.buttons.create = {
        name: 'create',
        className: 'buttons-create uk-button-primary ' + baseButtonClass,
        text: function (dt) {
            return '<span uk-icon="plus"></span> ' + dt.i18n('buttons.create', 'Create');
        },
        action: function (e, dt, button, config) {
            window.location = window.location.href.replace(/\/+$/, "") + '/create';
        }
    };

    DataTable.ext.buttons.collection = function (aa, bb, cc) {
        return {
            action: _handleCollection
        }
    }

    DataTable.ext.buttons.colvis = function (b, a) {
        return {
            name: 'colvis',
            extend: 'collection',
            text: function (dt) {
                return '<span uk-icon="list"></span> ' + dt.i18n('buttons.colvis', 'Column visibility')
            },
            action: _handleCollection,
            className: 'buttons-colvis ' + baseButtonClass,
            buttons: [{
                extend: 'columnsToggle',
                columns: a.columns,
                columnText: a.columnText
            }]
        }
    };

    DataTable.ext.buttons.columnsToggle = function (b, a) {
        return b.columns(a.columns).indexes().map(function (c) {
            return {
                extend: 'columnToggle',
                columns: c,
                columnText: a.columnText
            }
        }).toArray()
    };

    DataTable.ext.buttons.columnToggle = function (b, a) {
        return {
            extend: 'columnVisibility',
            columns: a.columns,
            columnText: a.columnText,
            className: baseButtonClass
        }
    };

    DataTable.ext.buttons.columnVisibility = function (b, a, l) {
        return {
            columns: l, text: function (b, a, c) {
                return c._columnText(b, c)
            },
            className: 'buttons-columnVisibility uk-width-1-1 uk-button-small uk-text-capitalize ' + visibleToggleColClass,
            action: function (b, a, c, d) {
                b = a.columns(d.columns);
                a = b.visible();
                b.visible(d.visibility !== l ? d.visibility : !(a.length && a[0]))
                $(c).toggleClass(visibleToggleColClass);
            },
            init: function (b, a, c) {
                const d = this;
                a.attr('data-cv-idx', c.columns);
                b.on(`column-visibility.dt${c.namespace}`, function (h, k) {
                    k.bDestroying || k.nTable !== b.settings()[0].nTable || d.active(b.column(c.columns).visible())
                }).on(`column-reorder.dt${c.namespace}`, function (h, k, m) {
                    if (b.columns(c.columns).count() && (d.text(c._columnText(b, c)))) {
                        d.active(b.column(c.columns).visible());
                    }
                });
                this.active(b.column(c.columns).visible())
            },
            destroy: function (b, a, c) {
                b.off(`column-visibility.dt${c.namespace}`).off(`column-reorder.dt${c.namespace}`)
            },
            _columnText: function (b, a) {
                let c = b.column(a.columns).index(), d = b.settings()[0].aoColumns[c].sTitle;
                d || (d = b.column(c).header().innerHTML);
                d = d.replace(/\n/g, ' ').replace(/<br\s*\/?>/gi, ' ').replace(/<select(.*?)<\/select>/g, ``).replace(/<!--.*?-->/g, '').replace(/<.*?>/g, '').replace(/^\s+|\s+$/g, ``);
                return a.columnText ? a.columnText(b, c, d) : d
            }
        }
    };

    DataTable.ext.buttons.colvisRestore = function (b, a, c) {
        return {
            name: 'colvisRestore',
            className: baseButtonClass + ' uk-text-capitalize',
            text: function (dt) {
                return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.colvisRestore', 'Restore visibility')
            },
            init: function (b, a, c) {
                c._visOriginal = b.columns().indexes().map(function (d) {
                    return b.column(d).visible()
                }).toArray()
            },
            action: function (b, a, c, d) {
                a.columns().every(function (h) {
                    h = a.colReorder && a.colReorder.transpose ? a.colReorder.transpose(h, 'toOriginal') : h;
                    this.visible(d._visOriginal[h])
                })
                $('.buttons-columnVisibility').each(function (idx, btn) {
                    $(btn).addClass(visibleToggleColClass);
                })
            },
        };
    }
})
    (jQuery, jQuery.fn.dataTable, UIkit);
