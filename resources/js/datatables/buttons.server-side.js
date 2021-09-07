(function ($, DataTable) {
    "use strict";

    const _buildParams = function (dt, action, onlyVisibles) {
        const params = dt.ajax.params();
        params.action = action;
        params._token = $('meta[name="csrf-token"]').attr('content');

        if (onlyVisibles) {
            params.visible_columns = _getVisibleColumns();
        } else {
            params.visible_columns = null;
        }

        return params;
    };

    const _getVisibleColumns = function () {
        const visible_columns = [];
        $.each(DataTable.settings[0].aoColumns, function (key, col) {
            if (col.bVisible) {
                visible_columns.push(col.name);
            }
        });

        return visible_columns;
    };

    const _downloadFromUrl = function (url, params) {
        const postUrl = url + '/export';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', postUrl, true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function () {
            if (this.status === 200) {
                let filename = "";
                const disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                const type = xhr.getResponseHeader('Content-Type');

                const blob = new Blob([this.response], {type: type});
                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    const URL = window.URL || window.webkitURL;
                    const downloadUrl = URL.createObjectURL(blob);

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        const a = document.createElement("a");
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.open(downloadUrl, '_blank');
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                        }
                    } else {
                        window.open(downloadUrl, '_blank');
                    }

                    setTimeout(function () {
                        URL.revokeObjectURL(downloadUrl);
                    }, 100); // cleanup
                }
            }
        };
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send($.param(params));
    };

    const _buildUrl = function (dt, action) {
        const url = dt.ajax.url() || '';
        const params = dt.ajax.params();
        const colVisibility = {
            columns: dt.columns().visible().toArray().map((value) => {
                return {show: value};
            })
        };
        params.action = action;

        const append = $.param(params) + '&' + $.param(colVisibility);
        if (url.indexOf('?') > -1) {
            return url + '&' + append;
        }
        return url + '?' + append;
    };

    const _handleCollection = function (event, dt, button, config) {
        event.stopPropagation();
        if (!config._collection.parents('body').length) {
            const collection = config._collection[0];
            collection.className += ' uk-card uk-card-body uk-card-default uk-padding-small dt-uk-collection';

            const targetContainer = button.parent();
            targetContainer.append(collection);

            UIkit.drop(collection, {mode: 'click',});
            /// Hack needed to show the drop after adding the list
            /// this is because the click event happened before the drop was created
            setTimeout(() => {
                button[0].click();
            }, 0);
        }
    }

    const baseButtonClass = 'uk-button uk-button-default uk-width-1-1@s';
    const visibleToggleColClass = 'uk-button-primary';

    DataTable.ext.buttons.excel = {
        className: 'buttons-excel ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="database"></span> ' + dt.i18n('buttons.excel', 'Excel');
        },

        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'excel'), '_blank');
        }
    };

    DataTable.ext.buttons.postExcel = {
        className: 'buttons-excel ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="database"></span> ' + dt.i18n('buttons.excel', 'Excel');
        },

        action: function (e, dt, button, config) {
            const url = dt.ajax.url() || window.location.href;
            const params = _buildParams(dt, 'excel');

            _downloadFromUrl(url, params);
        }
    };

    DataTable.ext.buttons.postExcelVisibleColumns = {
        className: 'buttons-excel ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="database"></span> ' + dt.i18n('buttons.excel', 'Excel (only visible columns)');
        },

        action: function (e, dt, button, config) {
            const url = dt.ajax.url() || window.location.href;
            const params = _buildParams(dt, 'excel', true);

            _downloadFromUrl(url, params);
        }
    };

    DataTable.ext.buttons.export = {
        extend: 'collection',

        className: 'buttons-export ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="cloud-download"></span> ' + dt.i18n('buttons.export', 'Export') + '&nbsp;<span class="caret"/>';
        },

        buttons: ['csv', 'excel', 'pdf']
    };

    DataTable.ext.buttons.csv = {
        className: 'buttons-csv ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="list"></span> ' + dt.i18n('buttons.csv', 'CSV');
        },

        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'csv'), '_blank');
        }
    };

    DataTable.ext.buttons.postCsvVisibleColumns = {
        className: 'buttons-csv ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="list"></span> ' + dt.i18n('buttons.csv', 'CSV (only visible columns)');
        },

        action: function (e, dt, button, config) {
            const url = dt.ajax.url() || window.location.href;
            const params = _buildParams(dt, 'csv', true);

            _downloadFromUrl(url, params);
        }
    };

    DataTable.ext.buttons.postCsv = {
        className: 'buttons-csv ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="list"></span> ' + dt.i18n('buttons.csv', 'CSV');
        },

        action: function (e, dt, button, config) {
            const url = dt.ajax.url() || window.location.href;
            const params = _buildParams(dt, 'csv');

            _downloadFromUrl(url, params);
        }
    };

    DataTable.ext.buttons.pdf = {
        className: 'buttons-pdf ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="file-pdf"></span> ' + dt.i18n('buttons.pdf', 'PDF');
        },

        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'pdf'), '_blank');
        }
    };

    DataTable.ext.buttons.postPdf = {
        className: 'buttons-pdf ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="file-pdf"></span>' + dt.i18n('buttons.pdf', 'PDF');
        },

        action: function (e, dt, button, config) {
            const url = dt.ajax.url() || window.location.href;
            const params = _buildParams(dt, 'pdf');

            _downloadFromUrl(url, params);
        }
    };

    DataTable.ext.buttons.print = {
        className: 'buttons-print ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="print"></span>' + dt.i18n('buttons.print', 'Print');
        },

        action: function (e, dt, button, config) {
            window.open(_buildUrl(dt, 'print'), '_blank');
        }
    };

    DataTable.ext.buttons.reset = {
        className: 'buttons-reset ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.reset', 'Reset');
        },

        action: function (e, dt, button, config) {
            dt.search('');
            dt.columns().search('');
            dt.draw();
        }
    };

    DataTable.ext.buttons.reload = {
        className: 'buttons-reload ' + baseButtonClass,

        text: function (dt) {
            return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.reload', 'Reload');
        },

        action: function (e, dt, button, config) {
            dt.draw(false);
        }
    };

    DataTable.ext.buttons.create = {
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
            extend: 'collection',
            text: function (dt) {
                return '<span uk-icon="list"></span> ' + dt.i18n('buttons.colvis', 'Column visibility')
            },
            action: _handleCollection,
            className: 'buttons-colvis ' + baseButtonClass,
            buttons: [{extend: 'columnsToggle', columns: a.columns, columnText: a.columnText}]
        }
    };

    DataTable.ext.buttons.columnsToggle = function (b, a) {
        return b.columns(a.columns).indexes().map(function (c) {
            return {extend: 'columnToggle', columns: c, columnText: a.columnText}
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
            text: function (dt) {
                return '<span uk-icon="refresh"></span> ' + dt.i18n('buttons.colvisRestore', 'Restore visibility')
            }, init: function (b, a, c) {
                c._visOriginal = b.columns().indexes().map(function (d) {
                    return b.column(d).visible()
                }).toArray()
            }, action: function (b, a, c, d) {
                a.columns().every(function (h) {
                    h = a.colReorder && a.colReorder.transpose ? a.colReorder.transpose(h, 'toOriginal') : h;
                    this.visible(d._visOriginal[h])
                })
                $('.buttons-columnVisibility').each(function (idx, btn) {
                    $(btn).addClass(visibleToggleColClass);
                })
            },
            className: baseButtonClass + ' uk-text-capitalize'
        };
    }
})
(jQuery, jQuery.fn.dataTable);
