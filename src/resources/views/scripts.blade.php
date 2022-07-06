<?php
$locale = __("datatables") === 'datatables' ? __("datatables::datatables") : __("datatables");

?>

<script type="text/javascript">
    window.classes = {};
    /**
     * Generate unique ID
     *
     * @param {String} prefix
     * @returns {String}
     */
    function uniqueId(prefix = "_")
    {
        return prefix + Math.random().toString(36).substr(2, 9);
    }
    
    /**
     * Reload the datatable
     */
    function dataTableReload()
    {
        classes["{{ $view->tableId }}"].refresh(@json($view));
    }


    classes["{{ $view->tableId }}"] = new class
    {

        /**
         * Constructor {{ $view->tableId }} table
         *
         * @returns {void}
         */
        constructor()
        {

        }

        /**
         * Init the Datatable
         *
         * @param {Object} view
         * @returns {void}
         */
        init(view)
        {
            this.loadSearchableColumns(view);

            this.loadFilters(view);

            this.loadConfig(view);

            this.build(view.tableId);

            this.loadTriggers(view);
        }

        /**
         * Build the table
         *
         * @param {String} id
         * @returns {void}
         */
        build(id)
        {
            this.table = $(`#${id}`).DataTable(this.config);
        }

        /**
         * Reload the table
         *
         * @param {Object} view
         */
        reload(view)
        {
            this.table.ajax.url(this.buildUrl(view)).load();
        }
        
        /**
         * Refresh the table content
         */
        refresh()
        {
            this.table.ajax.reload( null, false );
        }

        /**
         * Load the table triggers
         *
         * @param {Object} view
         * @returns {unresolved}
         */
        loadTriggers(view)
        {
            if (view.autoReload) {
                setInterval(() => {
                    this.reload(view);
                }, 10000);
            }

            $(document).on('input', '.search-filter', this.reload.bind(this, view));
            $(document).on('click', `#${ view.tableId }_wrapper .paginate_button`, this.rememberPage.bind(this, view));
        }

        /**
         * Remember the page on each trigger
         *
         * @param {Object} view
         * @returns {void}
         */
        rememberPage(view)
        {
            if (!view.rememberPage) {
                return false;
            }

            let url = location.href.split(`datatables-page=`)[0];

            if (url.slice(-1) === '?' || url.slice(-1) === '&') {
                url = url.slice(0, -1);
            }

            let mark = url.includes('?') ? "&" : "?";

            window.history.pushState('laravel-datatable', 'pagination', `${url}${mark}datatables-page=${this.table.page.info().page + 1}`);
        }

        /**
         * Trigger searchable columns
         *
         * @param {Object} view
         * @returns {void}
         */
        searchFilterInput(view)
        {
            let filterSearch = `&filtersearch=`;

            $(`#${ view.tableId } .search-filter`).each((i, input) => {
                if ($(input).val().length > 0) {
                    filterSearch += `${$(input).attr('name')};${$(input).val()}|`;
                }
            });

            return filterSearch;
        }

        /**
         * Set locale
         *
         * @param {Object} translations
         * @returns {void}
         */
        locale(translations)
        {
            this.translations = translations;

            return this;
        }

        /**
         * Load the searchable columns
         *
         * @param {Object} view
         * @returns {void}
         */
        loadSearchableColumns(view)
        {
            view.columns.forEach((column, index) => {
                if (column.columnSearch) {
                    $(`#${ view.tableId }`).find(`thead th:nth-child(${index + 1})`).append(`
                    <input type="text" name="${ column.data }" class="search-filter" placeholder="${this.translations.columnSearchLabel}" />
                `);
                }
            });
        }

        /**
         * Load the table filters
         *
         * @param {Object} view
         * @returns {void}
         */
        loadFilters(view)
        {
            view.filters.forEach((filter) => {
                $(`#${ view.tableId }datatable-filters`).append(`${filter.build}`);
                $(`#${filter.filterId}`).on(filter.filterTrigger, this.reload.bind(this, view));
            });
        }

        /**
         * Build the filter string
         *
         * @returns {void}
         */
        filterChanged()
        {
            let filters = `&filter=`;

            $(`.datatable-filter`).each((index, e) => {
                if (!$(e).val() || $(e).val() === '**' || !$(e).attr('id')) {
                    return;
                }
                let type = $(e).attr('multiple') ? 'm' : 's';

                filters += `${$(e).attr('name')};${type}*${$(e).val()}|`;
            });

            return filters;
        }

        /**
         * Load the configs
         *
         * @param {Object} view
         * @returns {void}
         */
        loadConfig(view)
        {
            this.config = {
                language: this.translations,
                paging: true,
                processing: true,
                serverSide: true,
                displayStart: this.displayStart(view),
                dom: view.dom,
                ajax: this.buildUrl(view),
                pageLength: view.pageLength,
                lengthMenu: view.pageMenu,
                order: view.order,
                columns: view.columns,
                columnDefs: this.buildColumnDefs(view.defs),
                createdRow: (row, data, index) => {

                    $(row).on('click', (el) => {
                        $(`#{{ $view->tableId }}`).trigger(`dtrow:click`, [row, data, this.table, el]);
                    });
                    
                    $(row).on('dblclick', (el) => {
                        $(`#{{ $view->tableId }}`).trigger(`dtrow:dblclick`, [row, data, this.table, el]);
                    });

                    $(row).on('mouseenter', (el) => {
                        $(`#{{ $view->tableId }}`).trigger(`dtrow:mouseenter`, [row, data, this.table, el]);
                    });

                    $(row).on('mouseleave', (el) => {
                        $(`#{{ $view->tableId }}`).trigger(`dtrow:mouseleave`, [row, data, this.table, el]);
                    });

                    $(`#{{ $view->tableId }}`).trigger(`dtrow:render`, [row, data, this.table]);
                },
                drawCallback: (settings) => {
                    $(`#{{ $view->tableId }}`).trigger(`dttable:render`, [settings, this.table.data(), this.table]);
                }
            };
        }

        /**
         * Return the start page
         *
         * @param {Object} view
         * @returns {Integer}
         */
        displayStart(view)
        {
            if (view.rememberPage && this.getParameterFromUrl('datatables-page')) {
                return (this.getParameterFromUrl('datatables-page') - 1) * view.pageLength;
            }

            return 0;
        }

        /**
         * Build the column defs
         *
         * @param {Object} defs
         * @returns {void}
         */
        buildColumnDefs(defs)
        {
            const defsArray = [];
            //loop the columns

            for (const [key, def] of Object.entries(defs)) {
                //push to the column defs array
                defsArray.push({
                    "class": def.class || '',
                    "render": (processData, type, row) => {
                        //the output returned after the render is completed
                        let output = "";
                        //loop the rendered views
                        for (let [index, rendered] of Object.entries(def.rendered)) {
                            //set the viewer
                            let data = processData;
                            let view = def.def[index];
                            //check the conditions
                            if (view.condition && !eval(`row.${view.condition}`)) {
                                continue;
                            }

                            //overwrite the data key
                            if (view.overwrite) {
                                data = this.overWriteData(view, row);
                            }

                            //Empty check
                            if (view.emptyCheck && (!data || (isNaN(data) && !data.length && !this.isJson(data)))) {
                                output += `${view.before || ''} ${view.returnWhenEmpty || ''} ${view.after || ''}`;
                                continue;
                            }

                            //run the rendered eval
                            eval(`
                            function ${def.id}${def.index}(){
                                ${rendered}
                            }
                            output += this.parseKeyView(row, ${def.id}${def.index}());
                        `);

                            output = this.revertHashed(this.parseKeyView(row, output));
                        }

                        return output;
                    },
                    "targets": def.target
                });
            }

            //return the columns
            return defsArray;
        }

        /**
         * @param {Object} row
         * @param {String} hashed
         * @returns {String}
         */
        parseKeyView(row, hashed)
        {
            if (!hashed.includes('{') || !hashed.includes('}')) {
                return hashed;
            }

            let sub = hashed.substr(hashed.indexOf('{') + 1);

            let key = sub.substr(0, sub.indexOf('}'));

            let value = false;
            
            try {
                value = eval(`row.${key}`);
            } catch (err) {
                return this.parseKeyView(row, hashed.replace(`{${key}}`, `${key}`));
            }
                                    
            if (value) {
                hashed = hashed.replace(`{${key}}`, value);
            } else {
                hashed = hashed.replace(`{${key}}`, `${key}`);
            }

            if (hashed.includes('{') && hashed.includes('}')) {
                return this.parseKeyView(row, hashed);
            }

            return hashed;
        }

        /**
         * @param {String} hashed
         * @returns {String}
         */
        revertHashed(hashed)
        {
            if (!hashed.includes('@$')) {
                return hashed;
            }
            
            return this.revertHashed(hashed.replace('@$', '#'));
        }

        /**
         * Check if parameter is a json string
         * 
         * @param {String} item
         * @type Arguments
         */
        isJson(item)
        {
            item = typeof item !== "string"
                    ? JSON.stringify(item)
                    : item;

            try {
                item = JSON.parse(item);
            } catch (e) {
                return false;
            }

            if (typeof item === "object" && item !== null) {
                return true;
            }

            return false;
        }

        /**
         * Return the new data key
         *
         * @param {Object} view
         * @param {Object} row
         * @returns {void}
         */
        overWriteData(view, row)
        {
            let split = view.overwrite.split('.');

            let newKey = false;

            split.forEach((item) => {
                newKey = newKey ? newKey[item] : row[item];
            });

            return newKey;
        }

        /**
         * Return the URL
         *
         * @param {Object} view
         * @returns {String}
         */
        buildUrl(view)
        {
            let uri = location.href;
            let mark = uri.includes('?') ? '&' : '?';
            let filters = this.filterChanged();
            let filterSearch = this.searchFilterInput(view);

            return `${uri}${mark}laravel-datatables=active&tableId=${view.tableId}&id=${view.id}${filters}${filterSearch}`;
        }

        /**
         * retutn the selected parameter
         *
         * @param {String} param
         * @returns {String} string
         */
        getParameterFromUrl(param)
        {
            return new URLSearchParams(window.location.search).get(param);
        }

    };


    classes["{{ $view->tableId }}"].locale(@json($locale)).init(@json($view));

</script>
