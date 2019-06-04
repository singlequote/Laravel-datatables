# Docs
* [Table models](https://singlequote.github.io/Laravel-datatables/table-models)
* [Fields](https://singlequote.github.io/Laravel-datatables/fields)
* [Translations](https://singlequote.github.io/Laravel-datatables/translations)

# Quick start
In the demo below we will render a table for the users. 

`UserController.php`
```php
use App\User;

public function index()
{
    $datatable = \DataTable::model(User::class)->tableModel(\App\TableModels\Users::class);
    //or
    $datatable = \DataTable::model(new User)->tableModel(\App\TableModels\Users::class);
    
    return view('users.index')->with(compact('datatable'));
}
```

`users/index.blade.php`
```php
<body>
    {!! $datatable->table() !!}
    
    {!! $datatable->script() !!}
</body>
```

`TableModel`
As you can see, a tablemodel is used to render the table. You can use the artisan command `php artisan make:table-model {name}` command to generate a new tableModel. See the docs for table models here [TableModels](https://singlequote.github.io/Laravel-datatables/table-models)