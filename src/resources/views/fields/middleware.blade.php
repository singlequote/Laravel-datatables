
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
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
    
    return `{!! $class->before !!} <label class="{{ $class->class }}">${output}</label> {!! $class->after !!}`;
</script>