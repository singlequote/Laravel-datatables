
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    let url = "#";
    @if($class->route)
    let url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
    url = url.replace("{{ $key }}", row.{{ $replace }});
    @endforeach
    @endif

    @if($class->src)
        let route = "{{ $class->src }}";
    @endif

    return `{!! $class->before !!} <img title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}" src="${url}" /> {!! $class->after !!}`;
</script>
