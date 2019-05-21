
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    let route = data;

    @if($class->route)
    let url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
//    if(!row.{{ $replace }}){
//        url = url.replace("{{ $key }}", "{{ str_replace('*', '', $key) }}");
//    }else{
        url = url.replace("{{ $key }}", row.{{ $replace }});
//    }
    
    @endforeach
    route = `${url}`;
    @endif

    return `{!! $class->before !!} <img class="{{ $class->class }}" src="${route}" /> {!! $class->after !!}`;
</script>