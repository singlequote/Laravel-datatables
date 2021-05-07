
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
    if(typeof row.{{ $replace }} !== 'undefined'){
        url = url.replace("{{ $key }}", row.{{ $replace }}).replace("{{ $key }}", row.{{ $replace }}).replace("{{ $key }}", row.{{ $replace }});
    }else{
        url = url.replace("{{ $key }}", `{{ $replace }}`).replace("{{ $key }}", `{{ $replace }}`).replace("{{ $key }}", `{{ $replace }}`);
    }
    @endforeach
    route = `href="${url}"`;
    @endif

    let label = !`{{ $class->label }}`.length ? data : `{{ $class->label }}`;
    
    @if($class->label)
        if(row.{{ str_replace(" ", "_", preg_replace('/[^A-Za-z0-9. -]/', '', $class->label)) }}){
            label = row.{{ str_replace(" ", "_", preg_replace('/[^A-Za-z0-9. -]/', '', $class->label)) }};
        }
    @endif
    
    let template = `
        <a ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" ${route} id="${id}" class="{{ $class->class }}">${label} {!! $class->icon !!}</a>
    `;

    return `{!! $class->before !!} ${template} {!! $class->after !!}`;
</script>
