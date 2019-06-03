
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

    @if($class->feather)
        icon = `<i class="{{ $class->class }}" data-feather="{{ $class->feather }}"></i>`;
        $(document).ready(() => {
           feather.replace();
        });
    @endif

    @if($class->material)
        icon = `<i class="material-icons {{ $class->class }}">{{ $class->material }}</i>`;
    @endif

    @if($class->fa)
        icon =  `<i class="fa fa-{{ $class->fa }}"></i>`;
    @endif

    return `{!! $class->before !!} ${icon} {!! $class->after !!}`;

</script>