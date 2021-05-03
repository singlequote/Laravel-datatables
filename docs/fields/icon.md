# Icon
The icon field can be used to add an icon to the column. A few default options can be used to display an icon

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Custom icon
Can be used to display a custom icon. 

```php
Icon::make('...')->custom('my-icon', optional 'edit')

// <i class="my-icon">edit</i>
```

### Font Awesome icon
Can be used to display a font awesome icon. 

```php
Icon::make('...')->custom('fa fa-edit')

// <i class="fa fa-edit"></i>
```

### Feather icon
Can be used to display a feather icon. 

```php
Icon::make('...')->custom('edit')

// <i data-feather="edit"></i>
```

### Material icon
Can be used to display a material icon. 

```php
Icon::make('...')->material('edit')

// <i class="material-icons">edit</i>
```