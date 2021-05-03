
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->

<script>

    let id = uniqueId("button_");
    
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
    route = `data-route="${url}"`;
    @endif
    
    let label = !`{{ $class->label }}`.length ? '' : `{{ $class->label }}`;
        
    @if($class->label)
    if(row.{{ $class->label }}){
        label = row.{{ $class->label }};
    }
    @endif

    @if($class->method === 'GET')
    $(document).on('click', `#${id}:not(.prevent)`,  (e) => {

        @if($class->target === 'blank')
            window.open($(e.currentTarget).data('route'), '_blank');
        @else
            location.href = $(e.currentTarget).data('route');
        @endif

    });
    let template = `
        <button ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" onclick="{{ $class->onClick }}" type="button" ${route} id="${id}" class="{{ $class->class }}">${ label } {!! $class->icon !!}</button>
    `;
    @elseif(in_array($class->method, ['POST', 'DELETE', 'PUT', 'PATCH']))
    let template = `
        <button ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" onclick="{{ $class->onClick != "" ? $class->onClick : '$("#form${id}").submit();' }}" type="button" id="${id}" class="{{ $class->class }}">${ label } {!! $class->icon !!}</button>
        <form class="laravel-datatable-form-{{ strtolower($class->method) }}" style="display:none;" method="post" id="form${id}" action="${url}">@csrf @method($class->method)</form>
    `;
    @endif

    return `{!! $class->before !!} ${template} {!! $class->after !!}`;
</script>
