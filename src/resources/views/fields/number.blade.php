
<!--
Everything between the script tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row inside the script tags

-->


<script>
    
    let dataAttributes = ``;
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} || `{{ $attribute }}` }" `;
    @endforeach
    
    let value = {{ !is_null($class->startAt) ? "parseFloat($class->startAt)" : 'data' }};

    @if($class->sum)
        value = 0;
        @foreach($class->sum as $sum)
        value += parseFloat(row.{{ $sum }});
        @endforeach
    @endif

    @if($class->sumEach)
        $.each(data, (index, item) => {
            @foreach($class->sumEach as $sum)
                value += parseFloat(item.{{ $sum }});
            @endforeach
            
            @if($class->times)
                value = value * item.{{ $class->times }};
            @endif
        });
    @endif
    
    

    @if($class->raw)
    return value;
    @endif

    @if($class->format)
    return parseFloat(value).toFixed({{ $class->decimals }});
    @endif
    
    @if($class->asCurrency)
    var p = parseFloat(value).toFixed({{ $class->decimals }}).split(".");

    return `{!! $class->before !!} 
            <label ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">
                ${p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                    return  num=="." ? acc : num + (i && !(i % 3) ? '{{ $class->thousands_sep }}' : "") + acc;
                }, "") + '{{ $class->dec_point }}' + p[1]}
            </label>
            {!! $class->after !!}`;
    @endif
</script>
