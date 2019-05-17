# Eloquent datatables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/acfbentveld/laravel-datatables.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-datatables)
[![Total Downloads](https://img.shields.io/packagist/dt/acfbentveld/laravel-datatables.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-datatables)

This repo contains a Datatable that can render a filterable and sortable table. It aims to be very lightweight and easy to use. It has support for retrieving data asynchronously, pagination and recursive searching in relations.

## Note
Users who want to use the older version of this package, go to the [early version repo](https://github.com/ACFBentveld/Laravel-datatables). The ACFBentveld group is transferd to a new group called SingleQuote.

## Installation

> The package is tested on laravel 5.8.*

You can install the package via composer:
```bash
composer require singlequote/laravel-datatables
```

## Whats new
Yes, we live in a time where everything is automated. So in this version we introduce some very cool features.
In this version the html and the script is created for you. Also this version is based on how laravel nova works.

## Let's start
We wanted our code as clean as possible and use the same code more than once. SO let's start with creating a `TableModel`. This `TableModel` controls your table. In the example below we are going to create a table with users.

> This package requires JQuery

### TableModel
Create a new file (for example inside `App\TableModels`) with the name `Users`.
```php
<?php
namespace App\TableModels;
//required abstract class
use SingleQuote\DataTables\Controllers\ColumnBuilder;

class Users extends ColumnBuilder
{
    /**
     * Set the table headers
     * Define the columns needed
     *
     * @var array
     */
    public $columns = [
        'id',
        'name',
        'email',
        'created_at'
    ];
    
    /**
     * Change the behaviour of the fields displayed by the resource
     *
     * @return array
     */
    public function fields() : array
    {
        return [
            //
        ];
    }
}
```

### Displaying the table
When you have created your `TableModel` we can use it in the controllers. In this example we create a `UsersController` and display the table in the `users` blade.

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datatable = \DataTable::model(new User)->tableModel(\App\TableModels\Users::class);
        
        return view('users.index')->with(compact('datatable'));
    }
}
```
Now when everything is set we can display the table inside our view.
```html
<body>
    <!--This displays the complete table-->
    {!! $datatable->table() !!}
</body>

<!--This displays the complete scripts including the <script> tags-->
{!! $datatable->script() !!}
```
Thats it. You table is all done and set. You are probably thinking, how can i change the behaviour of the columns like changing the format of the created_at column. 

### Changing the behaviour of the columns
Yes you can change the behaviour of the columns with `Field classes`. Inside your `TableModel` find the method `fields`.
Inside the `return [];` you can define the behaviour of the columns. For example we are going to change the format of the `created_at` column to the format `d-m-Y H:i`

```php
use SingleQuote\DataTables\Fields\Date; //include the fields class

/**
 * Change the behaviour of the fields displayed by the resource
 *
 * @return array
 */
public function fields() : array
{
    return [
        Date::make('created_at')->format('d-m-Y') //Thats it
    ];
}
```

#### Fields
---
**Label**
use as | `Date::make(column_name)`
| Method | parameter | Example     | Description |
|--------|-----------|-------------|-------------|
| none |  none   | `Date::make(column_name)` | Return default value |
---
**Date**
use as | `Date::make(column_name)`
| Method | parameter | Example     | Description |
|--------|-----------|-------------|-------------|
| format | string    | `Date::make(column_name)->format("y-m-d")` | Format column as date |
---
**Number**
use as | `Number::make(column_name)`
| Method | parameter | Example     | Description |
|--------|-----------|-------------|-------------|
| asCurrency | `int` $decimals, `string` $dec_point, `string` $thousands_sep | `Number::make(column_name)->asCurrency(2, ',', '.')` | Format column as Number in currency format |
---

### TableModel behaviour
The TableModel behaviour is by default simple and fast but you can change this like adding a query tot the model or translate the headers of the table.

---
**Query**
To execute a query on your model you can use the `query` method to perform queries on your model.
```php
/**
 * Perform a query on the model resource
 *
 * @param \Illuminate\Database\Eloquent\Model $query
 * @return \Illuminate\Database\Eloquent\Model
 */
public function query($query)
{
    return $query->whereHas('role', 'admin')->where('can_login', true);
}
```
---
**Searchable**
If you want to limit or add extra columns to the search area you can use the `searchable` property. For example if you only want to search in the columns `email` and `name`. By default the `$columns` define your search keys.
```php
public $searchable = [
    'name', 'email'
];
```
___
**Encrypting keys**
If you want to encrypt a key for example the id of the user, you can use the `encrypt` property to set the keys that need to be encrypted.
```php
public $encrypt = [
    'id'
];
```

___
**Table ID**
For every table a unique id is rendered but if you want to add a custom ID you can use the `tableId` property.
```php
public $tableId = "my-table-id";
```
___
**Table class**
When you are using bootstrap you propably want to add the `table` class to your tables. You can use the `tableClass` property to add classes.
```php
public $tableClass = "table myTableClass someOtherClass";
```
___
**Header translation**
You don't want columns like `created_at` in your tablehead and translate it to something else. You can use the `translate` method for this.
```php
/**
 * Set the translations for the header
 *
 * @return array
 */
public function translate() : array
{
    return [
        'created_at' => __("Created At") //translate the column created_at to something else
    ];
}
```
___


### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: ACF Bentveld, Ecu 2 8305 BA, Emmeloord, Netherlands.

## Credits

- [Wim Pruiksma](https://github.com/wimurk)
- [Amando Vledder](https://github.com/AmandoVledder)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
