
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->

<script>
    let route;
    @if($class->route)
    let url = "{{ $class->route }}";
    @foreach($class->routeReplace as $key => $replace)
    url = url.replace("{{ $key }}", row.{{ $replace }});
    @endforeach
    route = `data-route="${url}"`;
    @endif

    @if($class->method === 'GET')
    $(document).on('click', '#{{ $class->id }}',  (e) => {
        location.href = $(e.currentTarget).data('route');
    });
    let template = `
        <button type="button" ${route} id="{{ $class->id }}" class="{{ $class->class }}">{!! $class->icon !!}</button>
    `;
    @elseif(in_array($class->method, ['POST', 'DELETE', 'PUT', 'PATCH']))
    let template = `
        <button onclick="$('#form{{ $class->id }}').submit()" type="button" id="{{ $class->id }}" class="{{ $class->class }}">{!! $class->icon !!}</button>
        <form class="laravel-datatable-form-{{ strtolower($class->method) }}" style="display:none;" method="post" id="form{{ $class->id }}" action="${url}">@csrf @method($class->method)</form>
    `;
    @endif

    return `{!! $class->before !!} ${template} {!! $class->after !!}`;
</script>
