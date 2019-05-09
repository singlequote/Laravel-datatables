
<!--
Everything between the < script > tags will be replaced with the datatbales render method like below
The script methods will be replaced with

"render": function ( data, type, row ) {
    //the code between the script tags
}

you can use the variables data, type and row

-->


<script>
    let date = new Date(data);
    let format = "{{ $class->format }}";
    let month = date.getMonth() + 1;
    month = month < 10 ? `0${month}` : month;
    let day = date.getDate();
    day = day < 10 ? `0${day}` : day;

    format = format.replace('Y', date.getFullYear());
    format = format.replace('y', date.getYear());
    format = format.replace('m', month);
    format = format.replace('D', date.getDay() + 1);
    format = format.replace('d', day);
    format = format.replace('H', date.getHours());
    format = format.replace('i', date.getMinutes());
    format = format.replace('s', date.getSeconds());

    return format;
</script>