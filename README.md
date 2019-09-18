# Laravel Cacher
A powerfull cacher based on laravels file cache driver
[![Latest Version on Packagist](https://img.shields.io/packagist/v/singlequote/laravel-cacher.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-cacher)
[![Total Downloads](https://img.shields.io/packagist/dt/singlequote/laravel-cacher.svg?style=flat-square)](https://packagist.org/packages/singlequote/laravel-cacher)


### Installation
```bash
composer require singlequote/laravel-cacher
```

## Usage

### Model Caching
Sometimes you just want to cache the model results. With model caching you can without making a mess in your code. Include the `cacher` trait within your model.
```php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable; //<= basic shizzle
use Illuminate\Contracts\Auth\MustVerifyEmail; //<= basic shizzle

use SingleQuote\Cacher\Traits\Cacher; //⇐ this one

class User extends Authenticatable implements MustVerifyEmail
{
	use Cacher; //<=  require the model to use the trait
}
```

Now whenever you call your `App\User` model, you can use the caching trait

```php
use App\User;

$users = User::whereEmail('foo@bar.world')->remember(); //<= default ttl is 7 days
$users = User::whereEmail('foo@bar.world')->remember(3600); //<= 1 hour

//or keep it forever by your side <3
$users = User::whereEmail('foo@bar.world')->rememberForever();
```

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
