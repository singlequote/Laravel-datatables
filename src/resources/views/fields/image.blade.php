
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
        url = url.replace("{{ $key }}", row.{{ $replace }});
    @endforeach
    route = `${url}`;
    @endif

    @if($class->src)
        let route = "{{ $class->src }}";
    @endif

    return `{!! $class->before !!} <img class="{{ $class->class }}" src="${route}" /> {!! $class->after !!}`;
</script>