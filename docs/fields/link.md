
# Link
The link class replaces a value from the results with an default html a tag.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Default link
Below is an example on how to generate a default html link.

```php
Link::make('name')->route('users.show', 'id')
```
The link field can be extended with a lot of available methods. Such as classes and routes. 

#### Class
Add dom classes to the link

```php
Link::make('name')->route('users.show', 'id')->class('btn btn-primary'),
// <a class="btn btn-primary"></a>
```

#### Icon
Add dom classes to the link

```php
Link::make('name')->route('users.show', 'id')->icon('material-icons', optional 'edit'),
// <a> <i class="material-icons">edit</i> </a>

Link::make('name')->route('users.show', 'id')->icon('fa fa-edit'),
// <a> <i class="fa fa-edit"></i> </a>
```

#### Route
Add an route to the link. The link will automatic redirect the user to the given route when clicked.
Variables can be passed directly from the results

```php
Link::make('name')->route('users.edit', optional 'id'),
// <a href="https://www.example.com/users/1/edit"> </a>
Link::make('name')->route('users.edit', ['id', 'status' => 'status_id']), //arrays can also be passed
// <a href="https://www.example.com/users/1/edit?status_id=2"> </a>
```
By default the package handles the routes passed to the `route` method. In some cases you want to prevent the click on the a.
In that case you can add the class 'prevent' tot the field. The package will ignore all `get` clicks.

```php
Link::make('name')->class('prevent')->route('users.edit', 'id'), //will ignore the clicks on the 
```

### Change target window
The target method can be used to open a blank window. It depends on the route method

```php
Link::make('name')->route('users.show', 'id')->target('blank'),

// <a href="https://www.example.com/users/1/edit"> </a>
```

### Add label
The label method can be used to add a label to the link

```php
Link::make('name')->route('users.show', 'id')->label('Edit user'),

// <a href="...">Edit user</a>

//The label method also accepts table columns.
Link::make('name')->route('users.show', 'id')->label('id')
// <a href="...">12</a>

```


### Full example
```php
Link::make('name')->route('users.show', 'id')->class('btn btn-warning')->icon('fa fa-edit')->label('Edit user')->target('blank');

```

```html
<a class="btn btn-warning" href="https://www.example.com/users/1/edit"> <i class="fa fa-edit"></i> Edit user</a>
```
