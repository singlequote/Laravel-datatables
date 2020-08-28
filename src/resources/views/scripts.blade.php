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


classes[`{{ $view->tableId }}`] = new class
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
     * Load the table triggers
     *
     * @param {Object} view
     * @returns {unresolved}
     */
    loadTriggers(view)
    {
        if(view.autoReload){
            setInterval(() => {
                reloadTable(view);
            },10000);
        }

        $(document).on('input', '.search-filter', this.reload.bind(this, view));
        $(document).on('click', `#${ view.tableId }_wrapper .paginate_button`, this.rememberPage.bind(this, view));
    }

    /**
     * Remember the page on each trigger
     *
     * @returns {void}
     */
    rememberPage(view)
    {
        if(!view.rememberPage){
            return false;
        }
        
        let url = location.href.split(`datatables-page=`)[0];

        if(url.slice(-1) === '?' || url.slice(-1) === '&'){
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
            if($(input).val().length > 0){
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
     */
    loadSearchableColumns(view)
    {
        view.columns.forEach((column, index) => {
            if(column.columnSearch){
                $(`#${ view.tableId }`).find(`thead th:nth-child(${index +1})`).append(`
                    <input type="text" name="${ column.data }" class="search-filter" placeholder="${this.translations.columnSearchLabel}" />
                `);
            }
        });
    }

    /**
     * Load the table filters
     *
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
            if(!$(e).val() || $(e).val() === '**' || !$(e).attr('id')){
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
            "language" : this.translations,
            "paging": true,
            "processing": true,
            "serverSide": true,
            "displayStart" : this.displayStart(view),
            "dom" : view.dom,
            "ajax": this.buildUrl(view),
            "pageLength" : view.pageLength,
            "order" : view.order,
            "columns" : view.columns,
            "columnDefs": this.buildColumnDefs(view.defs)
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
        if(view.rememberPage && this.getParameterFromUrl('datatables-page')){
            return (this.getParameterFromUrl('datatables-page') -1) * view.pageLength;
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
                "class" : def.class || '',
                "render": ( data, type, row ) => {
                    //the output returned after the render is completed
                    let output = "";
                    
                    //loop the rendered views
                    for (const [index, rendered] of Object.entries(def.rendered)) {
                        //set the viewer
                        let view = def.def[index];

                        //overwrite the data key
                        if(view.overwrite){
                            data = this.overWriteData(view, row);
                        }
                        
                        //check the conditions
                        if(view.condition && !eval(`row.${view.condition}`)){
                            continue;
                        }

                        //Empty check
                        if(view.emptyCheck && (!data || (isNaN(data) && !data.length))){
                            output += `${view.before || ''} ${view.returnWhenEmpty || ''} ${view.after || ''}`;
                            continue;
                        }

                        //run the rendered eval
                        eval(`
                            function ${def.id}${def.index}(){
                                ${rendered}
                            }
                            output += ${def.id}${def.index}();
                        `);
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


classes[`{{ $view->tableId }}`].locale(@json($locale)).init(@json($view));

</script>
