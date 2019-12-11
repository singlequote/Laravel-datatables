
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
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} }" `;
    @endforeach
    
    return `{!! $class->before !!} <label ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">${data}</label> {!! $class->after !!}`;
</script>