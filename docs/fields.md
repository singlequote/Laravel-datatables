# Fields
Almost every table has buttons or date values that you want to format or add classses to. With datatables you can use Field classes. The field classes create the buttons or format the date, currency etc. for you.

<= [Go back](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------

## Example
Below is a simple example on how to use fields inside your [tableModel](https://singlequote.github.io/Laravel-datatables/table-models). 

In this example we are going to add an edit button and format a date string.
Always add the field classes to the `fields` method inside your [tableModel](https://singlequote.github.io/Laravel-datatables/table-models).
```php
public $columns = [
    'id',
    'created_at'
];


public function fields() : array
{
    return [
        // This will replace the value id with this button. The button will be clickable and has an icon and 2 classes
        Button::make('id')->icon("fa fa-edit")->class('class1 class2')->route('my-route.edit', 'id'),
        //This will replace the created_at value with an formatted value. The format will be day-month-year hour:minutes
       Date::make('created_at')->format('d-m-Y H:i')
    ];
}
```

## Default options
Every field class has the same default options. For example every field class can have an custom class and ID.
You can use the default options on every field class. In the examples below we will use the Label class.
```php
Label::make('...')->class('class1 class2')
//or
Date::make('...')->class('class1 class2')
```

### Class
Add a class to an field class. 
```php
Label::make()->class('class1 class2 class3')
```

### Column
By default the value of the `make` method is used to display the data. You can overwrite this to show other data.
In the example below, we will display the `updated_at` value in the `created_at` column row.
```php
Label::make('created_at')->column('updated_at')
```

### Conditions
When using permissions, sometimes you want to hide data from certain users. You can use a filter for this or use the condition method on your field class.
```php
//Display the created_at value if the updated_at value is not null
Label::make('created_at')->condition('updated_at !== null')
```

### returnWhenEmpty
By default when a value or relation is missing, the response will be null (white space).
```php
Label::make('role.name')->returnWhenEmpty("Role not found")
```

### Before
The before method can be used to display text or icons before the value is displayed.
```php
Label::make('role.name')->before('Role : ') // Role : Administrator
```

### After
The before method can be used to display text or icons before the value is displayed.
```php
Label::make('temperature')->after('degrees') // 19 degrees
```

## Field classes

### Label (default)
This is the default field class. When there is no field class specified for a column, the package will use this class to show the data.
```php
Label::make('...')
```


