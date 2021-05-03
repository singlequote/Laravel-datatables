# Label
The label field is the default field class. Every column will by default be translated to the label field.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Displaying the label
Use the laravel routing to display an image. The paramaters will be auto formatted to the results value.
If a parameter does not exists it will be left untouched.

```php
Label::make('name')

//<label> John doe </label>
```
