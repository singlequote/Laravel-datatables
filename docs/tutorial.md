# Tutorial creating a users table
In this tutorial we will create a users table starting with the basic

<= [Go back](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------

## Creating the tableModel
The packages provides a handful commands to quickly start building your tables. For instance the command `php artisan make:table-model {name} --options` creates a new table model.

In the tutorial we will create a table model for the users using the buttons, translations, route and query option
```console
php artisan make:table-model UsersTable --buttons --translations --route=users --query
```
As you can see in the above command, a new table class is generated with a few predefined methods.
```php
	// @var array
    public $tableClass = "";
        

    /**
     * Set the columns for your table
     * These are also your header, searchable and filter columns
     *
     * @var array
     */
    public $columns = [
        'name',
        [
            "data"          => "id",
            "name"          => "id",
            "class"         => "my-class",
            "orderable"     => false,
            "searchable"    => false
        ]
    ];

    
   /**
    * Set the translation columns for the headers
    *
    * @return array
    */
    public function translate() : array
    {
        return [
            'name' => __('Name'),
        ];
    }
            
    
   /**
    * Run an elequent query
    *
    * @param \Illuminate\Database\Query\Builder $query
    * @return \Illuminate\Database\Query\Builder
    */
    public function query($query)
    {
        return $query->whereNotNull('id');
    }
            

    /**
     * Alter the fields displayed by the resource.
     *
     * @return array
     */
    public function fields() : array
    {
        return [
            Button::make('id')->class('my-button-class')->route('users.show', 'id'),
            Button::make('id')->class('my-button-class')->route('users.edit', 'id'),
            Button::make('id')->class('my-button-class')->method('delete')->route('users.destroy', 'id'),
        ];
    }
```

## Using the table
To display the table you have to call it in your controller. let's assume you already have a `UserController`. If not, take  a look at the [laravel docs on how to create a controller](https://laravel.com/docs/master/controllers)

The `DataTable` expects 2 resources. The `Table` class and the `model` you want to perform the actions on.
In your `index method` call the `DataTable` facade and pass the users `model` like below

```php
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datatable = \DataTable::model(\App\User::class)
	        ->tableModel(\App\TableModels\Users::class);
        
        return view('users.index')->with(compact('datatable')); //⇐ pass the variable to the view
    }
```

## Setting up the view
We will create a view named `index.blade.php` in the directory `resources/views/users`
Only 2 methods are available to display the table, the table content and the scripts.
```php
@extends('layouts.app')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class='row'>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
	                // Generate the table
                    {!! $datatable->table() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
@endsection

@push('js') 
//push the scripts
{!! $datatable->script() !!}
@endpush
```
In the demo above we push the scripts to the stack array. [Check out the docs ](https://laravel.com/docs/7.x/blade#stacks). Or you can place it somewhere else.
> Remember that the package requires the [https://datatables.net/](https://datatables.net/) scripts!

*The table is ready for use now*

-------------------------------------------------------------------------------------------------------------------------------


## Filling the table model
We start by defining the table class using the `public $tableClass` property. This will add a class to the table.

```php
public $tableClass = "table table-bordered"
``` 

Next we will fill the columns. By default the command will add the `name` and `id` columns. We will add the columns `email` and `created_at` columns.
```php
public $columns = [
	'id',
    'name',
    'email'
    'created_at'
];
```
The above will output the columns we need but isn't formatting anything. Next we format the `created_at` to a readable date format. 

On top of your `table model` include the [Date](https://singlequote.github.io/Laravel-datatables/fields/date) field.
`use SingleQuote\DataTables\Fields\Date`

next we tell the table model to use the Date field and assign it to the `created_at` column.
Add the Date field to the `fields` method in your `table model`
```php

    /**
     * Alter the fields displayed by the resource.
     *
     * @return array
     */
    public function fields() : array
    {
        return [
			Date::make('created_at')->format('d-m-Y') //dd-mm-YYYY
        ];
    }
```
The above will display a formatted created at column but the table doesn't perform any actions. Next we will create 3 buttons that execute 3 actions. `show`, `edit` and `delete`. 

In this tutorial i assume you use the [laravel resources](https://laravel.com/docs/7.x/controllers#resource-controllers) controllers. If not, change the routes.

**Add the buttons field**
Next include the [Button](https://singlequote.github.io/Laravel-datatables/fields/button) field on top of your `table model` 
`use SingleQuote\DataTables\Fields\Button;`

Next in your `fields` method, we assign the button field to the `id` column and redirect the action to the `users.show` route.

```php
  public function fields() : array
  {
      return [
		Date::make('created_at')->format('d-m-Y'), //dd-mm-YYYY
		
		Button::make('id')->class('btn btn-primary')->icon('material-icons', 'account_circle')->route('users.show', 'id'),
		Button::make('id')->class('btn btn-warning')->icon('material-icons', 'edit')->route('users.edit', 'id')
      ];
  }
```
Now the tabel will generate 2 buttons that redirects to the passed routes. Checkout the [Button](https://singlequote.github.io/Laravel-datatables/fields/button) docs on how to style the button.
The above Buttons use the `GET` method to perform the action. Next we will add the `delete method`
Add a new Button to the `fields` method, this time we add the `method` attribute to the table

```php
  public function fields() : array
  {
      return [
		Date::make('created_at')->format('d-m-Y'), //dd-mm-YYYY
		
		Button::make('id')->class('btn btn-primary')->icon('material-icons', 'account_circle')->route('users.show', 'id'),
		Button::make('id')->class('btn btn-warning')->icon('material-icons', 'edit')->route('users.edit', 'id'),
		
		Button::make('id')->class('btn btn-danger')->icon('material-icons', 'trash')->route('users.destroy', 'id')->method('delete')
      ];
  }
```
Now the table will know that you want to perform a `DELETE` action and will generate a form with the inputs `_token` and `_method`.

### Digging deeper
The buttons are assigned to the `id` column. But what if we want to show the `id` of the users and place the buttons at the end of the table? Let's edit the columns method again, but this time add a new column for the buttons.

```php
public $columns = [
	'id',
    'name',
    'email',
    'created_at',
    [
	    'name' => 'actions',
	    'data' => 'id'
    ]
];
```
As showen above, we added a new column `id` but named `actions`, Now the table can use the same resources or multiple columns. Next change the buttons assignment. 
```php
  public function fields() : array
  {
      return [
		Date::make('created_at')->format('d-m-Y'), //dd-mm-YYYY
		
		Button::make('actions')->class('btn btn-primary')->icon('material-icons', 'account_circle')->route('users.show', 'id'),
		Button::make('actions')->class('btn btn-warning')->icon('material-icons', 'edit')->route('users.edit', 'id'),
		
		Button::make('actions')->class('btn btn-danger')->icon('material-icons', 'trash')->route('users.destroy', 'id')->method('delete')
      ];
  }
```

> Happy coding
