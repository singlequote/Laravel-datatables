
<!--
Everything between the script tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row inside the script tags

-->


<script>

    let value = data;

    @if($class->sum)
        value = 0;
        @foreach($class->sum as $sum)
        value += parseFloat(row.{{ $sum }});
        @endforeach
    @endif

    if(!value || value === 0){
    return "{{ $class->returnWhenEmpty }}";
    }

    @if($class->format)
    return parseFloat(value).toFixed({{ $class->decimals }});
    @endif
    
    @if($class->asCurrency)
    var p = parseFloat(value).toFixed({{ $class->decimals }}).split(".");
    return p[0].split("").reverse().reduce(function(acc, num, i, orig) {
        return  num=="-" ? acc : num + (i && !(i % 3) ? '{{ $class->thousands_sep }}' : "") + acc;
    }, "") + '{{ $class->dec_point }}' + p[1];
    @endif
</script>