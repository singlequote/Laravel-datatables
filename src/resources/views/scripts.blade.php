<script type="text/javascript">

    $(document).ready(() => {
        
        $('#{{ $view->tableId }}').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "http://127.0.0.1:8000/acfbentveld/datatables",
            "columns": [
                @foreach($view->columns as $column)
                @json($column),
                @endforeach
            ],
            "columnDefs": [ 
                @foreach($view->defs as $def)
                {
                    "render": function ( data, type, row ) {
                        let output = "";
                        @foreach($def['rendered'] as $render)
                            function {{$def['id']}}(data, type, row) {
                                {!! $render !!}
                            };
                            
                            output += {{$def['id']}}(data, type, row);
                        @endforeach
                        
                        return output;
                    },
                    "targets": {{ $def["target"] }}
                },
                @endforeach
            ]
        });

    });

    
    
    


</script>