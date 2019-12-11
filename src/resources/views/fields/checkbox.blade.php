
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
    
    
    let checked = "";
    
    @if(is_string($class->checked))
        if(row.{{ $class->checked }}){
            checked = 'checked';
        }
    @else
        checked = "{{ $class->checked === true ? 'checked' : '' }}";
    @endif
    
    let template = `
            {{ $class->label }} 
            {!! $class->icon !!}
            <input ${ dataAttributes } ${checked} title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" onclick="{{ $class->onClick }}" id="{{ $class->id }}" class="{{ $class->class }}" type="checkbox" name="{{ $class->name }}">
        `;

    

    return `{!! $class->before !!} ${template} {!! $class->after !!}<label for="{{ $class->id }}"></label>`;
</script>
