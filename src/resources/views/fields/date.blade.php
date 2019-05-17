
<!--
Everything between the < script > tags will be replaced with the datatables render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    if(!data){
        return "{{ $class->returnWhenEmpty }}";
    }
    
    /**
     * Format item from 9 to 09
     *
     * @param {string} item
     * @returns {String}
     */
    function formatZero(item)
    {
        return item < 10 ? `0${item}` : item;
    }

    let date    = new Date(data);
    let format  = "{{ $class->format }}";

    let month   = formatZero(date.getMonth() + 1);
    let day     = formatZero(date.getDate());
    let hours   = formatZero(date.getHours());
    let minutes = formatZero(date.getMinutes());
    let seconds = formatZero(date.getSeconds());

    format = format.replace('Y', date.getFullYear());
    format = format.replace('y', date.getYear());
    format = format.replace('m', month);
    format = format.replace('D', date.getDay() + 1);
    format = format.replace('d', day);
    format = format.replace('H', hours);
    format = format.replace('i', minutes);
    format = format.replace('s', seconds);

    return format;
</script>