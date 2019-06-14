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

### Title
The title method can be user for tooltips or just the title element.
```php
Label::make('name')->title('Name of the user', optional 'tooltip') // 19 degrees
//<label title="Name of the user" data-toggle="tooltip">John Doe</label>
```

## Field classes

### Label (default)
This is the default field class. When there is no field class specified for a column, the package will use this class to display the data.
```php
Label::make('...')
```

### Date
Can change the format of a date string, from php format to javascript format.
```php
Date::make('...')->format('d-m-Y H:i:s')
```

### Button
Can place buttons inside your table. For example edit or delete buttons.
```
Button::make('...');
```
The following options can be used with this class

|Options|Description|Example|
|---|---|---|
|label (string)|Place label inside the button|`Button::make('...')->label('edit user')`|
|icon (class, name)|Place an icon inside the button|`Button::make('...')->icon('fa fa-edit')`|
|route|Make the button clickable|`Button::make('...')->route('users.edit', 'id')`|
|method|Change the route method of the button|`Button::make('...')->route('users.destroy', 'id')->method('delete')`|
|target|Change the target of the route|`Button::make('...')->route('users.edit', 'id')->target('blank')`|

### Icon
Insert an icon
```php
Icon::make('...')->feather('edit') //insert feather icon
-
Icon::make('...')->material('edit') //insert material icon
-
Icon::make('...')->fa('edit') //insert font awesome icon
```

### Image
Insert an image
```php
Image::make('...')->src("https://www.w3schools.com/howto/img_forest.jpg")
- 
//or if you use image routes
Image::make('...')->route("media", params)
```

### Multiple
The multiple field class let't you insert multiple field classes in one column or use a simple counter over relations.
**Implode**
Use the implode method on for example relations.
```php
Multiple::make('roles')->implode('name', ', '); //admin, employee, etc..
```
**Each**
It is also possible to use multiple field classes in one column. For example, the creation date of every role a user haves.
```php
Multiple::make('...')->each('roles', function(){
    return [
        Label::make('name')->after(', <br>'),
    ];
})
//will look like
//  Admin,
//  Maintainer,
//  etc..
```
**Counter**
Counting numbers or prices, sometimes you want to count a few numbers and display them. The counter method accepts an clusore function and requires fields to be returned
```php
Multiple::make('...')->count(function(){
    return [
        Number::make('...')->raw(), //return raw number
    ];
});
 ```
 
### Number
The number field class can be used to format numbers or to count numbers. It can also be formatted as currency.
```php
Number::make('...')->format(2) //default 2 decimals, returns number formatted as 2 decimals
-
Number::make('...')->raw() //returns the number as a raw format
- 
Number::make('...')->asCurrency(2, '.', ',') //default values, formatted as currency
```

**Sum**
Using the sum method, you can count the numbers and return as a formatted or raw number.
```php
Number::make('...')->sum('column1', 'column2', 'etc...')->format() //return formatted number
```

**Sum Each**
This can be used to sum columns of relations
```php
Number::make('products')->sumEach('price', 'tax')->asCurrency() //sum all given columns of the relation products
```

### Custom fields
Yes you can create custom fields classes, and yes we would love to use your field classes as a default field for this package. 
**Command**
Run the command `php artisan make:table-field {name}` to generate a new Field class.
This will create a new Field class in `App\TableModels\Fields` and a resource view in `resources/views/vendor/laravel-datatables/fields`.

You can now call your custom field class as `Fields\MyCustomField::make('...')`











