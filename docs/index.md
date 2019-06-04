# Docs
* [Table models](https://singlequote.github.io/Laravel-datatables/table-models)
* [Fields](https://singlequote.github.io/Laravel-datatables/fields)

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

## Translations
Translating the datatable is easy, create a new file in your language folder `resources/lang/en` with the name `datatables.php` and paste or translate the following :
 
 ```php
 <?php
return [
	"sEmptyTable" =>     "No data available in table",
	"sInfo" =>           "Showing _START_ to _END_ of _TOTAL_ entries",
	"sInfoEmpty" =>      "Showing 0 to 0 of 0 entries",
	"sInfoFiltered" =>   "(filtered from _MAX_ total entries)",
	"sInfoPostFix" =>    "",
	"sInfoThousands" =>  ",",
	"sLengthMenu" =>     "Show _MENU_ entries",
	"sLoadingRecords" => "Loading...",
	"sProcessing" =>     "Processing...",
	"sSearch" =>         "Search:",
	"sZeroRecords" =>    "No matching records found",
	"oPaginate" => [
		"sFirst" =>    "First",
		"sLast" =>     "Last",
		"sNext" =>     "Next",
		"sPrevious" => "Previous"
	],
	"oAria" => [
		"sSortAscending" =>  " => activate to sort column ascending",
		"sSortDescending" => " => activate to sort column descending"
	]
];
 ```
 [Here you can find the full list of translations](https://datatables.net/plug-ins/i18n/)
 
