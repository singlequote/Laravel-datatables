
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    let dataAttributes = ``;
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} || `{{ $attribute }}` }" `;
    @endforeach
    
    let url = "#";
    @if($class->route)
    url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
    if(typeof row.{{ $replace }} !== 'undefined'){
        url = url.replace("{{ $key }}", row.{{ $replace }}).replace("{{ $key }}", row.{{ $replace }}).replace("{{ $key }}", row.{{ $replace }});
    }else{
        url = url.replace("{{ $key }}", `{{ $replace }}`).replace("{{ $key }}", `{{ $replace }}`).replace("{{ $key }}", `{{ $replace }}`);
    }
    @endforeach
    @endif

    @if($class->src)
        url = "{{ $class->src }}";
    @endif

    return `{!! $class->before !!} <img ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}" src="${url}" /> {!! $class->after !!}`;
</script>
