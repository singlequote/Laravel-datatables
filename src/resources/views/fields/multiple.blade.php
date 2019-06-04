
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>

    let output = "";

    @if($class->fields)

        let values = [];

    @foreach($class->fields as $field)
    output = "";
    
     @php
         $id = uniqid("closure");
     @endphp

     function {{ $id }}(data, type, row){
         {!! $field["rendered"] !!}
     }


    $.each(row.{{ $field["path"] }}, (index, item) => {

        if(!values[index]){
            values[index] = [];
        }

         values[index].push({{ $id }}(item{{ strlen($field["column"]) > 0 ? ".".$field['column'] : '' }}));
    });
     

    $.each(values,  (index, items) => {
        $.each(items, (key, item) => {
            output += item;
        });
    });




    @endforeach

    @endif

    @if($class->count)
        output = 0;
        @foreach($class->count as $field)

        @php
            $id = uniqid("closure");
        @endphp

        function {{ $id }}(data, type, row){
            {!! $field["rendered"] !!}
        }

        output += {{ $id }}(row.{{ $field["column"] }}, type, row);

        @endforeach
    @endif


    @if($class->implode)
        output = row.{{ $class->implode['path'] }}.map((elem) => {
            return elem.{{ $class->implode['name'] }};
        }).join("{!! $class->implode['seperate'] !!}");
    @endif

    return `{!! $class->before !!} <label class="{{ $class->class }}">${output}</label> {!! $class->after !!}`;
</script>