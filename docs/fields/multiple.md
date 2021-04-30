# Multiple
The multiple field class makes it possible to show mutlipel field classes inside a single column. FOr example you can display the firstname and lastname of a user inside the same column. It is also possible to loop through relations to display the results inside a single column.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Multiple fields
To display multiple fields inside a single column (for example firstname and lastname of a user).

```php
Multiple::make('...')->fields(function(){
	return [
		Label::make('firstname'),
		Label::make('lastname')
	];
});
//returns John Doe
```

### Each multiple results
Can be used to loop through relations or multiple results. For example to display the role names connected to the user.
make sure that the $columns definition is using the singular name for example ```roles``` not "roles.name"
```php
Multiple::make('roles')->each('roles', function(){
	return [
		Label::make('name')->after(',')
	];
});
//Admin, Employee, Awesome dude, etc
```

### Implode relations or multiple results
Can be used like the default php implode method. Can be user on relations or multiple results.
For example to show the role names of a user.

```php
Multiple::make('roles.name')->implode(`, `);
//Admin, Employee, Awesome dude, etc
```

### Count fields
Can be used to count a certain value in an relation or multiple results.

```php
Multiple::make('products.price')->count(function(){
	return [
		Number::make('products')->sumEach('price')->raw(), //25.00 + 25.00
		Number::make('articles')->sumEach('price')->raw() //25.00 + 25.00
	];
});
//100.00
```
