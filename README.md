# Initial Laraval Spa Installation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/itstudioat/spa.svg?style=flat-square)](https://packagist.org/packages/itstudioat/spa)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/itstudioat/spa/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/itstudioat/spa/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/itstudioat/spa/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/itstudioat/spa/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/itstudioat/spa.svg?style=flat-square)](https://packagist.org/packages/itstudioat/spa)


# What is SPA
SPA is the initial Laravel installtion for a single page application.

It contains:
- Vue with Vue-Router as Javascript-Framework
- Pinia as store for vue
- Vuetify as vue component framework
- Sanctum as authentication system for SPAs 
- Vite for asset bundling

It provides:
- Login-page 
- Password-forgotten-page

The pages have all the necessary logistic behind 

## Installation

First install a new Laravel app
```bash
laravel new new-laravel-app
```

You can install the package via composer:

```bash
composer require itstudioat/spa
```

If you use Postmark for Mailing (i use it):
```bash
composer require symfony/postmark-mailer
composer require symfony/http-client
```

To install the laravel sanctum and when you are asked, lets run the migrations:
```bash
php artisan install:api

```


To integrate all packages like vue:
```bash
php artisan spa:install
npm install
```

Make storage:link
```bash
php artisan storage:link
```

At the first you can publish your vite.config.js like this:
```bash
php artisan vendor:publish --tag=spa-vite-config --force
```

Publishing the resources, make this after composer install and composer update
```bash
php artisan vendor:publish --tag=spa-assets
```

To inividualize the Mail-Logo, pusblish de Markdown files to resources/views/vendor/mail
```bash
php artisan vendor:publish --tag=laravel-mail
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

Add UserTrait, HasApiToken to User-Model (App/Models/User.php):
```bash
use Itstudioat\Spa\Traits\UserTrait;

class User extends Authenticatable
{
    use UserTrait;
    ...
```

For sanctum security add HasApiToken to User-Model:
```bash
use Itstudioat\Spa\Traits\UserTrait;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    ...
```

Change the User-Model from $fillable to $guarded
```bash
protected $guarded = [];
```    

Add the middleware to app/bootstrapp/app.php
```bash
    ->withMiddleware(function (Middleware $middleware) {
        ...
        $middleware->statefulApi();
        ...
    })
```

Be sure, in resources/js/bootstrap.js are following lines:
```bash
import axios from 'axios';
window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

```

Add the Throttle-functionality to app/Prividers/AppServiceProvider
```bash
public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(config('spa.api_throttle', 60))->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('web', function (Request $request) {
        return Limit::perMinute(config('spa.web_throttle', 60))->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(config('spa.global_throttle', 60));
    });
}
```

Make your necessary changes in .env and config/app.php


Add a user with following command:
```bash
    php artisan user:create
```


## Usage



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
