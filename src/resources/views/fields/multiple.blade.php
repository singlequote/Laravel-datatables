
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    
    let dataAttributesMultiple = ``;
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} || `{{ $attribute }}` }" `;
    @endforeach
    
    let output = "";
    
    @if($class->eachFields)
    //EACH FIELDS

    let values = [];

    @foreach($class->eachFields as $field)
    output = "";
    
     @php
         $id = uniqid("eachFields");
     @endphp
     function {{ $id }}(data, type, row){
         
         {!! $field["rendered"] !!}
     }

    $.each(row.{{ $field["path"] }}, (index, item) => {

        if(!values[index]){
            values[index] = [];
        }
        console.log();
        values[index].push({{ $id }}(item{{ strlen($field["column"]) > 0 ? ".".$field['column'] : '' }}, type, item));
    });
     

    $.each(values,  (index, items) => {
        $.each(items, (key, item) => {
            output += item;
        });
    });
    
    @endforeach
    //EACH FIELDS==============================================================
    @endif
    
    @if($class->fields)
        
    //SINGLE FIELDS
    @foreach($class->fields as $field)
    
    @php
        $fieldID = uniqid("fieldExtra");
    @endphp
    
    function {{ $fieldID }}(data, type, row){
        {!! $field["rendered"] !!}
    }
    
    output += {{ $fieldID }}(row.{{$field['column']}}, type, row);
    @endforeach
    //SINGLE FIELDS==============================================================
    @endif
    
    
    @if($class->count)
    //FIELD COUNTER
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
    //FIELD COUNTER==============================================================
    @endif


    @if($class->implode)
    //IMPLODE FIELD
        output = row.{{ $class->implode['path'] }}.map((elem) => {
            return elem.{{ $class->implode['name'] }};
        }).join("{!! $class->implode['seperate'] !!}");
    //IMPLODE FIELD==============================================================
    @endif


    @if($class->length)
    //LENGTH FIELD
        output = row.{{ $class->length }} ? row.{{ $class->length }}.length : 0;
    //LENGTH FIELD==============================================================
    @endif
    
    @if($class->decimals)
        let p = parseFloat(output).toFixed({{ $class->decimals }}).split(".");

        output =  `${p[0].split("").reverse().reduce(function(acc, num, i, orig) {
            return  num=="." ? acc : num + (i && !(i % 3) ? '{{ $class->thousands_sep }}' : "") + acc;
        }, "") + '{{ $class->dec_point }}' + p[1]}`;
    @endif
    
    return `{!! $class->before !!} <label ${ dataAttributesMultiple } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">${output}</label> {!! $class->after !!}`;
</script>
