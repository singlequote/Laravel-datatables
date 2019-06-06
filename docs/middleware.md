# Middleware | permissions and roles
Almost every table has buttons or date values that you want to format or add classses to. With datatables you can use Field classes. The field classes create the buttons or format the date, currency etc. for you.

<= [Go back](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------

## Field permissions
To filter the table with permissions you can use the `permissions method`

In the example we will use a button for editing a user
```php
    //permission `edit users`
    Button::make('id')->permission('edit users')->route('...')
    
    //permission `edit users` or the role `admin`
    Button::make('id')->permission('edit users')->role('admin')->route('...')
    
    //permission `edit users` or the permission `view users`
    Button::make('id')->permission('edit users|view users')->route('...')
    
    //permission `edit users` and the permission `view users`
    Button::make('id')->permission('edit users,view users')->route('...')
    
    //permission `edit users` and the permission `view users` or the permission `create users`
    Button::make('id')->permission('edit users,view users|create users')->route('...')
```

## Field roles
To filter the table with roles you can use the `role method`

```php
    //role `admin`
    Button::make('id')->role('admin')->route('...')
    
    //permission `edit users` or the role `admin`
    Button::make('id')->permission('edit users')->role('admin')->route('...')
    
    //role `admin` or the role `maintainer`
    Button::make('id')->role('admin|maintainer')->route('...')
    
    //role `admin` and the role `maintainer`
    Button::make('id')->role('admin,maintainer')->route('...')
    
    //role `admin` and the role `maintainer` or the role `superuser`
    Button::make('id')->permission('admin|maintainer|superuser')->route('...')
```

## Field wrapper
If you really want to prevent users from ready data that they are not allowed to see, you can use the Field wrapper.
This class removes the data completely from the data output.

You can use the exact same filters as shown above
```php
Middleware::make('id')->permission('edit users')->role('admin')->wrap(function(){
    return [
        Button::make('id')->route('...')
    ];
});
```
When the user does not have the permission `edit users` the data output in will look like this
```json
{
    id : null, //The ID column is null
    name : 'John Doe',
    email : hello@world.com,
    etc
}
```