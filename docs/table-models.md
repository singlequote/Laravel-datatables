# Table Models
Table models are the controllers for your tables. Inside this file you can configure the behaviour of the tables

<= [Go back](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------

## Create
You can use the `artisan` command to generate a new `TableModel`. Where `Users` is the name of the table model.
```php
php artisan make:table-model Users
```

This command also has some options you can use to easily create a new table model.

| Option | Description | Demo |
| ------------- | ------------- | ------------- |
| --route={value} | Set the route for the 3 demo buttons | `php artisan make:table-model Name --route=my-route` |
| --class={value} | Set the table class | `php artisan make:table-model Name --class=table` |
| --buttons | Set a default amount of buttons (show, edit, destroy) | `php artisan make:table-model Name --buttons` |
| --translations | Set a translations method | `php artisan make:table-model Name --translations` | 


## Caching results
It is possible to cache the results by using the elequent methods in your `tableModel` checkout the [Laravel Cacher](https://github.com/singlequote/laravel-cacher) package for the docs.

```php
    /**
     * The method used by the elequent builder.
     * For example : ->get() or if changed : ->rememberForever()
     *
     * @var string
     */
    public $elequentMethod = "rememberForever"; //remembers the data forever
    
    /**
     * The prefix string used by the cacher.
     * Default it generates an unique query string
     *
     * @var string
     */
    public $elequentPrefix = "users"; //the prefix for the cache file. remove or leave empty for unique generated string
```

## Fields
Almost every table has buttons or date values that you want to format or add classses to. With datatables you can use Field classes. The field classes create the buttons or format the date, currency etc. for you. See the full docs on how to use the Fields. [See docs for fields](https://singlequote.github.io/Laravel-datatables/fields)

## Methods and properties
Below is a list of methods and properties

### The columns
Inside the `columns` property you define your table columns. By defaut the table fills the attributes.
```php
    public $columns = [
        'name', //define the column. The package fills in the rest
        'last_name as lastname', //you can change the data name by using `as`
        [
            'data' => "id", //this is the data attribute
            'name' => "id", //this is the display attribute
            "class" => "td-actions", //this is the td class
            "searchable" => true, //set the column to be searchable
            "orderable" => true //set the column to be orderable
            "columnSearch" => false, //this generates a search input below the column header
        ]
    ];
```

### Remember paging property
Whats more frustrating then hitting the refresh button and you can't remember which data page you were on. Enable the property `$rememberPage` so the datatable will remember wich page you were on.
```php
/**
 * Remember the data page of the user
 *
 * @var bool
 */
public $rememberPage = true;
```

### Page length property
By default the pagelength for your table is `10` you can change this to whatever length you want.
```php
/**
 * The default pagelength
 *
 * @var int
 */
public $pageLength = 10;
```

### Page order property
By default the first column in your table is used to order the table. You can edit this with the `$order` property. You can also use multiple sorting on your columns. 
```php
/**
 * Set order
 *
 * @var mixed
 */
public $order = [
    [ 0, "asc" ] // sort first column as ASC
];
```

### Searchable property
It is possible to add a filter to the searchable headers. By default the columns passed in the `columns` property are used as searchables. 
```php
/**
 * Set the searchable keys
 *
 * @var array
 */
public $searchable = [
    //
];
```
### Row created function
This callback is executed when a TR element is created (and all TD child elements have been inserted), allowing manipulation of the TR element.

This is particularly useful when using deferred rendering (deferRender) or server-side processing (serverSide) so you can add events, class name information or otherwise format the row when it is created.
```php
/**
 * define the function as string
 *
 * @var string
 */
 public $createdRow = "function(row, data, dataIndex ) {
    $(row).data('id', data.id);
    if(data.status_id == 6){
        $(row).data('deleted', true);
        $(row).addClass('table-danger');
    }
}";



### Autoreloading the table
The table will reload the content every few seconds. Set the `autoReload` to false to disable the function 
```php
/**
 * When set to true.
 * The package will auto reload the content of the current table page
 * @var bool
 */
public $autoReload = false;
```

### Encrypt property
It is possible to encryt values from your data response. By default none values are encrypted. Return an array with the `data` name of the column.
```php
/**
 * Set the encrypted keys
 *
 * @var array
 */
public $encrypt = [
    //
];
```

### Custom table ID property
By default every table has it's own unique ID. You can change this calling the `$tableId` property.
```php
/**
 * Set the table id
 *
 * @var mixed
 */
public $tableId = "myCustomId";
```

### Table classes property
When using bootstrap, you probably want to use the `table` class on your tables. Call the `tableClass` property to add classes
```php
/**
 * Set the table classes
 *
 * @var string
 */
public $tableClass = "class1 class2 class3";
```

### Table Head classes property
When using bootstrap, you probably want to use the `thead-dark` class on your table headers. Call the `tableHeadClass` property to add classes
```php
/**
 * Set the table head classes
 *
 * @var string
 */
public $tableHeadClass = "class1 class2 class3";
```

### Translations method
When you have header names like `roles.name` or `permissions.name` you probably want to change this. Use the method `translate` to translate your headers
```php
/**
 * Set the translations for the header
 *
 * @return array
 */
public function translate() : array
{
    return [
        'roles.name' => __("app.name") //translate the column roles.name to name
    ];
}
```

### Query method
You might want to use filters on your data response or use scopes. You can use the `query` method to use the elequent `Builder`
```php
/**
 * Perform a query on the model resource
 *
 * @param \Illuminate\Database\Query\Builder $query
 * @return \Illuminate\Database\Query\Builder
 */
public function query($query)
{
    return $query->where('name', 'Like', "%John Doe%")->with('roles', 'permissions');
}
```
