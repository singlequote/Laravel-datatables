
# Custom fields
The package provides a few default fields wich can be used to parse the table. If you want to add your own fields, follow the steps below.

[Go back to datatables](https://singlequote.github.io/Laravel-datatables)

### Available fields
- [Multiple](https://singlequote.github.io/Laravel-datatables/fields/multiple)
- [Button](https://singlequote.github.io/Laravel-datatables/fields/button)
- [Date](https://singlequote.github.io/Laravel-datatables/fields/date)
- [Icon](https://singlequote.github.io/Laravel-datatables/fields/icon)
- [Image](https://singlequote.github.io/Laravel-datatables/fields/image)
- [Label](https://singlequote.github.io/Laravel-datatables/fields/label)
- [Middleware](https://singlequote.github.io/Laravel-datatables/fields/middleware)
- [Number](https://singlequote.github.io/Laravel-datatables/fields/number)
- [Checkbox](https://singlequote.github.io/Laravel-datatables/fields/checkbox)


### Creating a custom field
A field is nothing more than a class that passes data. Some methods are required.

#### Step 1
Create a new class file called `MyField`. You are free to create the class anywere you want. For example we will create the class inside `App\TableModels\Fields` where our table models are. 

#### Step 2 - the field class
Some methods are required so the package can parse the fields. Use the code below and paste it into your field class

```php
<?php
namespace App\TableModels\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of your field
 *
 */
class MyField extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "path.to.blade";

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new self;
        $class->column = $column;
        return $class;
    }
}
```
#### Step 3 - the blade
The field does need a blade file to parse your data. Because the package is serverside, the code is written in javascript.

Create a new blade file for example in  `resources/views/fields/my-field.blade.php`
Edit your Field class `view` method and change it `fields.my-field`. The blade requires some tags that are needed for the package.

Use the code below and paste it into your blade.
```html
 
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
    
    return `
	    {!! $class->before !!} 
	    
	    <label ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">
		    ${data}
		</label> 
		
		{!! $class->after !!}`;
</script>

```

Thats all.  Your field is now ready to be used

 #### Step 4 - using the field
Now in your tableModel you can include your field using the namespace and classname like below.

```php
 use App\TableModels\Fields\MyField;

    /**
     * Alter the fields displayed by the resource.
     *
     * @return array
     */
    public function fields() : array
    {
        return [
            MyField::make('column')
        ];
    }
    
```

#### Step 5
Now you can change the field in whatever you need inside your table. For example: we want to reverse a string.

Your blade will look like this:

```html
 <script>
    let dataAttributes = ``;
    
    @foreach($class->data as $key => $attribute)
        dataAttributes += `data-{{ $key }}="${ row.{{ $attribute }} || `{{ $attribute }}` }" `;
    @endforeach
	
	function reverseString(str) {
	    return str.split("").reverse().join("");
	}

    return `
	    {!! $class->before !!} 
	    
	    <label ${ dataAttributes } title="{{ $class->title['title'] }}" data-toggle="{{ $class->title['toggle'] }}" class="{{ $class->class }}">
		    ${ reverseString(data) } //reverse the column name
		</label> 
		
		{!! $class->after !!}`;
</script>
```
