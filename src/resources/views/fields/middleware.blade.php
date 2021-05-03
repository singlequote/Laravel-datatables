
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
    
    let output = "";
    
    @foreach($class->fields as $field)
    
    @php
         $id = uniqid("wrapClosure");
    @endphp
     
    function {{$id}}() 
    {
        {!! $field['rendered'] !!}
    }
     
    output += {{$id}}();
     
    @endforeach
    
    return `{!! $class->before !!} <label ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">${output}</label> {!! $class->after !!}`;
</script>
