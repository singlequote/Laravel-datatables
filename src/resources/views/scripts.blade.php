<script type="text/javascript">

    /**
     * Get a parameter from the url
     * 
     * @param {type} param
     * @returns {unresolved}
     */
    function getParameterFromUrl(param){
      return new URLSearchParams(window.location.search).get(param);
    }

    let uri = location.href;
    let mark = uri.includes('?') ? '&' : '?';

    let Json{{ $view->tableId }} = {
        "paging": true,
        "processing": true,
        "serverSide": true,
        "dom" : "{!! $view->dom !!}",
        "ajax": `${uri}${mark}laravel-datatables=active&id={{ $view->id }}`,
        "pageLength" : {{ $view->pageLength }},
        "columns": [
            @foreach($view->columns as $column)
            @json($column),
            @endforeach
        ],
        "columnDefs": [
            @foreach($view->defs as $def)
            {
                "class" : "{{ $def["class"] }}",
                "render": function ( data, type, row ) {
                    let output = "";
                    @foreach($def['rendered'] as $index => $render)
                        function {{$def['id']}}{{ $index }}(data, type, row) {
                            {!! $render !!}
                        };

                        output += {{$def['id']}}{{ $index }}(data, type, row);
                    @endforeach

                    return output;
                },
                "targets": {{ $def["target"] }}
            },
            @endforeach
        ]
    };
    @if($view->rememberPage)
    if(getParameterFromUrl('datatables-page')){
        Json{{ $view->tableId }}.displayStart = (getParameterFromUrl('datatables-page') -1) * Json{{ $view->tableId }}.pageLength;
    }
    @endif
    
    $(document).ready(() => {
        let Data{{ $view->tableId }} = $('#{{ $view->tableId }}').DataTable(Json{{ $view->tableId }});
    });

</script>