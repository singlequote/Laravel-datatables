
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

    let route;
    @if($class->route)
    let url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
    url = url.replace("{{ $key }}", row.{{ $replace }});
    @endforeach
    route = `data-route="${url}"`;
    @endif

    @if($class->method === 'GET')
    $(document).on('click', `#${id}`,  (e) => {

        @if($class->target === 'blank')
            window.open($(e.currentTarget).data('route'), '_blank');
        @else
            location.href = $(e.currentTarget).data('route');
        @endif
        
    });
    let template = `
        <button title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" type="button" ${route} id="${id}" class="{{ $class->class }}">{{ $class->label }} {!! $class->icon !!}</button>
    `;
    @elseif(in_array($class->method, ['POST', 'DELETE', 'PUT', 'PATCH']))
    let template = `
        <button title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" onclick="$('#form${id}').submit()" type="button" id="${id}" class="{{ $class->class }}">{{ $class->label }} {!! $class->icon !!}</button>
        <form class="laravel-datatable-form-{{ strtolower($class->method) }}" style="display:none;" method="post" id="form${id}" action="${url}">@csrf @method($class->method)</form>
    `;
    @endif

    return `{!! $class->before !!} ${template} {!! $class->after !!}`;
</script>
