
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    let icon;
    
    let dataAttributes = ``;
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} }" `;
    @endforeach

    @if($class->feather)
        icon = `<i ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}" data-feather="{{ $class->feather }}"></i>`;
        $(document).ready(() => {
           feather.replace();
        });
    @endif

    @if($class->material)
        icon = `<i ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="material-icons {{ $class->class }}">{{ $class->material }}</i>`;
    @endif

    @if($class->fa)
        icon =  `<i ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="fa fa-{{ $class->fa }}"></i>`;
    @endif

    @if($class->custom)
        icon =  `<i ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->custom }}">{{ $class->customName }}</i>`;
    @endif

    return `{!! $class->before !!} ${icon} {!! $class->after !!}`;

</script>