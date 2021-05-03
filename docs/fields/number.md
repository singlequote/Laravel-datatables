# Number
The number field can be used to format numbers or sum relation values or multiple values.

[Go back to fields](https://singlequote.github.io/Laravel-datatables/fields)

### Format as currency
The most well-known problem is to format a currency. Use the currency method to format to currency values.

```php
Number::make('price')->asCurrency(optional 2, optional '.', optional ',');

// 1,000.00

Number::make('price')->asCurrency(2, ',', '.');

//1.000,00
```

### Decimal format
To format a number to decimals.

```php
Number::make('number')->format(optional 2);

// 1000.00
```

### Return raw number
To format a number to decimals.

```php
Number::make('number')->raw();

// 1000
```

#### You can also sum multiple columns and format it with the above methods.

### Sum columns
To sum multiple columns from the results. Don't use it to count relation or multiple values.

```php
Number::make('...')->sum('price1', 'price2', 'price3')->asCurrency();

// 1,000.00
```

### Sum relation or multiple values
Can be used to sum columns in relations or multiple values

```php
//sums the price and tax of every product
Number::make('products')->sumEach('price', 'tax')->asCurrency();

// 1,000.00
```
