# Checkbox
The checkbox class replaces a value from the results with an default html checkbox.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Default checkbox
Below is an example on how to generate a default html input with type checkbox.

```php
Checkbox::make('email_verified_at')
```
The checkbox field can be extended with a lot of available methods. Such as classes and routes. 

#### Class
Add dom classes to the button

```php
Checkbox::make('email_verified_at')->class('my-custom-checkbox')
// <input type="checkbox" class="my-custom-checkbox"/>
```

#### Icon
Add dom classes to the button

```php
Checkbox::make('email_verified_at')->icon('material-icons', optional 'edit'),
// <i class="material-icons">edit</i> <input type="checkbox" />

Checkbox::make('email_verified_at')->icon('fa fa-edit'),
// <i class="fa fa-edit"></i> <input type="checkbox" />
```

#### Checked
The checkbox can set to checked when passing a value that is not null, false or int 0.

```php
Checkbox::make('email_verified_at')->checked(true)
// <input type="checkbox" checked="checked"/>
Checkbox::make('email_verified_at')->checked('deleted_at') //when value is null => false,
// <input type="checkbox" />
```


### Add onclick
The onclick method can be used to trigger an event.

```php
Checkbox::make('email_verified_at')->onclick("$('form').submit()"),

// <input type="checkbox" onclick="$('form').submit()"/>
```

### Full example
```php
Checkbox::make('email_verified_at')->class('my-checkbox')->checked('email_verified_at !== null')->name('email_verified_at')->data(['user_id' => 'id'])->onclick("updateEmailVerifiedAt(this)"),

```

```html
<input type="checkbox" onclick="updateEmailVerifiedAt(this)" class="my-checkbox" checked="checked" name="email_verified_at" data-user="1" />
```
