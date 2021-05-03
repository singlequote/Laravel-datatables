# Button
The button class replaces a value from the results with an default html button.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Default button
Below is an example on how to generate a default html button.

```php
Button::make('id'),
```
The buttun field can be extended with a lot of available methods. Such as classes and routes. 

#### Class
Add dom classes to the button

```php
Button::make('id')->class('btn btn-primary'),
// <button class="btn btn-primary"></button>
```

#### Icon
Add dom classes to the button

```php
Button::make('id')->icon('material-icons', optional 'edit'),
// <button> <i class="material-icons">edit</i> </button>

Button::make('id')->icon('fa fa-edit'),
// <button> <i class="fa fa-edit"></i> </button>
```

#### Route
Add an route to the button. The button will automatic redirect the user to the given route when clicked.
Variables can be passed directly from the results

```php
Button::make('id')->route('users.edit', optional 'id'),
// <button data-route="https://www.example.com/users/1/edit"> </button>
Button::make('id')->route('users.edit', ['id', 'status' => 'status_id']), //arrays can also be passed
// <button data-route="https://www.example.com/users/1/edit?status_id=2"> </button>
```
By default the package handles the routes passed to the `route` method. In some cases you want to prevent the click on the buttons.
In that case you can add the class 'prevent' tot the field. The package will ignore all `get` clicks.

```php
Button::make('id')->class('prevent')->route('users.delete', 'id'), //will ignore the clicks on the button
```


#### Post or delete resource
The method can be used to post,put or delete a resource. It depends on the route method.

```php
Button::make('id')->route('users.delete', 'id')->method('delete'),

//<button> </button>
//<form method="post" id="formbutton_prlwrinww" action="http://modbus/modbus/1"></form>
//  <input type="hidden" name="_method" value="DELETE">
//  <input type="hidden" name="_token" value="qqVdiXEFdwumIhvPVgtHoo0NlQA9sNmVHukMbhEa">
//</form>
```
### Change target window
The target method can be used to open a blank window. It depends on the route method

```php
Button::make('id')->route('users.delete', 'id')->target('blank'),

// <button data-route="https://www.example.com/users/1/edit"> </button>
```

### Add label
The label method can be used to add a label to the button

```php
Button::make('id')->label('edit user'),

// <button>edit user</button>
```
The label also accepts column data. For example when you want to show the users name inside a button.
```php
Button::make('id')->label('name'),

// <button>John Doe</button>
```

### Add onclick
The onclick method can be used to overwrite the onlick attribute. 

```php
Button::make('id')->onclick("$('form').submit()"),

// <button onclick="$('form').submit()">edit user</button>
```

### Full example
```php
Button::make('id')->class('btn btn-warning')->icon('fa fa-edit')->label('Edit user')->route('users.edit', 'id')->target('blank');

```

```html
<button class="btn btn-warning" data-route="https://www.example.com/users/1/edit"> <i class="fa fa-edit"></i> Edit user</button>
```
