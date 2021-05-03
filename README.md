# Eloquent datatables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/singlequote/laravel-datatables.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-datatables)
[![Total Downloads](https://img.shields.io/packagist/dt/singlequote/laravel-datatables.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-datatables)

This repo contains a Datatable that can render a filterable and sortable table. It aims to be very lightweight and easy to use. It has support for retrieving data asynchronously, pagination, permission check, role check, laravel policies and recursive searching in relations.

## Installation

> The package is tested on laravel 5.8.*, 6.*, 7.* and 8.*

You can install the package via composer:
```bash
composer require singlequote/laravel-datatables
```

## Let's start
We wanted our code as clean as possible and use the same code more than once.

[Tutorial](https://singlequote.github.io/Laravel-datatables/tutorial)

[See the Quick start docs here](https://singlequote.github.io/Laravel-datatables/)


## Whats new
* Added table triggers (events). [Check it here](https://singlequote.github.io/Laravel-datatables/table-models#table-triggers)
* Added a new field class called `Link`. It generates an html `a` tag [Check it here](https://singlequote.github.io/Laravel-datatables/fields/link)
* We upgraded the label method on the `Button` and `Link` class. You can use data columns now for showing server side data. [Check it here](https://singlequote.github.io/Laravel-datatables/fields/link#add-label)
* Writing your own custom field. Check it [here](https://singlequote.github.io/Laravel-datatables/custom-fields)
* You can use filters to add directly to your tableModel. Check it out [here](https://singlequote.github.io/Laravel-datatables/filters)
* Column search fields. This makes it easy to search on a single column. Check it out here [here](https://singlequote.github.io/Laravel-datatables/table-models)

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Quotec, Traktieweg 8c 8304 BA, Emmeloord, Netherlands.

## Credits

- [Wim Pruiksma](https://github.com/wimurk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
