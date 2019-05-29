<script type="text/javascript">
    
    /**
     * Get a parameter from the url
     * 
     * @param {type} param
     * @returns {unresolved}
     */
    function getParameterFromUrl(param)
    {
        return new URLSearchParams(window.location.search).get(param);
    }

    /**
     * Genrate unique ID
     *
     * @param {String} prefix
     * @returns {String}
     */
    function uniqueId(prefix = "_")
    {
        return prefix + Math.random().toString(36).substr(2, 9);
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
        "order" : [
            @foreach($view->order as $order)
            [
                {{ $order[0] }},
                "{{ $order[1] }}"
            ]
            @endforeach
        ],
        "columns": [
            @foreach($view->columns as $column)
            @json($column),
            @endforeach
        ],
        "columnDefs": [
            @foreach($view->defs as $def)
            {
                "class" : "{{ isset($def["class"]) ? $def["class"] : '' }}",
                "render": function ( data, type, row ) {
                    let output = "";
                    @foreach($def['rendered'] as $index => $render)

                        @php
                            $class = $def["def"][$index];
                        @endphp

                        function {{$def['id']}}{{ $index }}(data, type, row) {

                            @if($class->overwrite)
                                data = row.{{ $class->overwrite }};
                            @endif
                            @if($class->emptyCheck)
                            if(!data){
                                return "{!! $class->before !!} {{ $class->returnWhenEmpty }} {!! $class->after !!}";
                            }
                            @endif

                            @if($class->condition)
                                if(!row.{{ $class->condition }}){
                                    return "{!! $class->before !!} {{ $class->returnWhenEmpty }} {!! $class->after !!}";
                                }
                            @endif
                            

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