
# Date
The date field can be used to format a date string

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Format
To change the format of a date string

```php
Date::make('created_at')->format('d-m-Y H:i:s');

//31-01-2019 15:00:00
```
