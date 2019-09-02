# Filters
Filters can be used to filter your data. The filters can be used directly in your tableModel and filters the data server side. 

<= [Go back](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------

## Basic example
In this example we will use a drop-down to filter the users. Let's create a filter to filter deleted users.
In your `tableModel` create a function named `filter`
```php
    use SingleQuote\DataTables\Filter\Dropdown;
    
    /**
     * Create filters
     *
     * @return array
     */
    public function filter() : array
    {
        $filter = [
            [
                'name' => "Without deleted", 
                "id" => 1
            ],[
                'name' => "With deleted", 
                "id" => 2
            ]
        ];
        
        return [
	        //the data attribute accepts an array as an object|collection
            Dropdown::make('filter')->label("Filter deleted")->data($filter),
        ];
    }
```
The above will create a dropdown filter above your datatable.
Now let's use the filter. Create a function named `query` in your `tableModel`
You can call the method `$this->getFilter(...)` to retrieve your filters data.
```php
    public function query($query)
    {
        if ($this->getFilter('filter') && (int) $this->getFilter('filter') === 2) {
            return $query->withTrashed(); //return with deleted users
        }
                
        return $query;
    }
```

## Data attribute
The data attribute is used to show the filters. As in the example you can pass an array or object to create your filter. By default the labels `name` and `id` are used to create the filter. See the options below.

#### array | object
Pass an array or object to create a filter.
```php
$filter = [
    [
        'name' => "Without deleted", 
        "id" => 1
    ],[
        'name' => "With deleted", 
        "id" => 2
    ]
];
        
Dropdown::make('filter')->label("Filter deleted")->data($filter),
```

### Model | collections
By default you the method uses the `id` as value and the `name` as label.
```php
$data = Status::all(); // {id : 1, name : 'invited', etc...}

Dropdown::make('filter')->label("Filter by status")->data($data),
```

### Closure
You can edit the `label` or `value` with an closure.

```php
$data = Status::all(); // {id : 1, name : 'invited', etc...}

Dropdown::make('filter')->label("Filter by status")->data($data, function($status){
	return [
		'label' => $status->name,
		'value' => $status->id
	];
}),
```