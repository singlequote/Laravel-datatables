
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->

<script>
    let id = uniqueId("link_");
    
    let dataAttributes = ``;
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} || `{{ $attribute }}` }" `;
    @endforeach

    let route;
    @if($class->route)
    let url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
    url = url.replace("{{ $key }}", row.{{ $replace }});
    @endforeach
    route = `href="${url}"`;
    @endif

    let label = !`{{ $class->label }}`.length ? data : `{{ $class->label }}`;
    
    @if($class->label)
    if(row.{{ $class->label }}){
        label = row.{{ $class->label }};
    }
    @endif
    
    let template = `
        <a ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" ${route} id="${id}" class="{{ $class->class }}">${label} {!! $class->icon !!}</a>
    `;

    return `{!! $class->before !!} ${template} {!! $class->after !!}`;
</script>
