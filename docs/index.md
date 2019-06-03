# Quick start
In the demo below we will render a table for the users. 

`UserController.php`
```php
use App\User;

public function index()
{
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


 
