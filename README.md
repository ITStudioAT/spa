# Initial Laraval Spa Installation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/itstudioat/spa.svg?style=flat-square)](https://packagist.org/packages/itstudioat/spa)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/itstudioat/spa/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/itstudioat/spa/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/itstudioat/spa/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/itstudioat/spa/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/itstudioat/spa.svg?style=flat-square)](https://packagist.org/packages/itstudioat/spa)


## Installation

You can install the package via composer:

```bash
composer require itstudioat/spa
```

This package requires:

- "laravel/sanctum for authorization"

If you use Postmark for Mailing:
```bash
composer require symfony/postmark-mailer
composer require symfony/http-client
```

To integrate all packages like vue:
```bash
php artisan aps:install
npm install
```

At the first you can publish your vite.config.js like this:
```bash
php artisan vendor:publish --tag=spa-vite-config --force
```
But be careful. With the --force your are overwriting your own changes in this file.
We will document here, what should be in the vite.config.js
```bash

```

Add UserTrait to User-Model:
```bash
use Itstudioat\Traits\UserTrait\UserTrait;

class User extends Authenticatable
{
    use UserTrait;
    ...
```



You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="spa-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="spa-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="spa-views"
```

## Usage

```php
$spa = new Itstudioat\Spa();
echo $spa->echoPhrase('Hello, Itstudioat!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Guenther Kron](https://github.com/itstudioat)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
