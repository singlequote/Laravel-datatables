# Middleware
The middleware field can be used to add middlewares or exclusions to the column. It not only prevents displaying the value but it will also remove the value from the json output.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Using roles
The roles method can be used to filter data using the role system. The role methods works well with the [Spatie permission package](https://github.com/spatie/laravel-permission)

```php
//if the user does not have the role `admin` the button will be removed 
//and all the id's will be removed from the data source
Middleware::make('id')->role('admin')->wrap(function(){
    return [
        Button::make('id')
    ];
});
```

### Using permissions
The permission method can be used to filter data using the permissions system. The permission methods works well with the [Spatie permission package](https://github.com/spatie/laravel-permission)

```php
//if the user does not have the permissions `edit users` the button will be removed 
//and all the id's will be removed from the data source
Middleware::make('id')->permission('edit users')->wrap(function(){
    return [
        Button::make('id')
    ];
});
```
[Visit the middleware page for all the options](https://singlequote.github.io/Laravel-datatables/middleware)

### Using laravel policy permissions
When using laravel policies you can pass the model as a second parameter. When passing `model` the middleware replaces the stirng with the actual resource.
```php
Middleware::make('id')->permission('edit', 'model')->role('admin')->wrap(function(){
    //..
});

Middleware::make('id')->permission('create', 'App\User')->role('admin')->wrap(function(){
    //..
});
```

### Using both

```php
Middleware::make('id')->permission('edit users')->role('admin')->wrap(function(){
    return [
        Button::make('id')->route('...')
    ];
});
```

### Multiple roles and permissions

```php
Middleware::make('id')->permission('edit users | can edit all')->role('user manager | admin')->wrap(function(){
    return [
        Button::make('id')->route('...')
    ];
});
```
