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

To install the laravel sanctum and when you are asked, lets run the migrations:
```bash
php artisan install:api

```

Now add HasApiToken to User-Model:
```bash
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

You can install the package via composer:

```bash
composer require itstudioat/spa
```

If you use Postmark for Mailing (i use it):
```bash
composer require symfony/postmark-mailer
composer require symfony/http-client
```


To integrate all packages like vue:
```bash
php artisan spa:install
npm install
```

Make storage:link and create  /storage/app/public/images folder
put the favicon.ico and logo.png in this folder
```bash
php artisan storage:link
```

Add the HasRoles-Trait to the App/Models/User.php
```bash
use HasRoles;
```

Publishing Spatie-Permission
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan optimize:clear
php artisan migrate
```

Publishing the resources
```bash
php artisan vendor:publish --tag=spa-assets
```

At the first you can publish your vite.config.js like this:
```bash
php artisan vendor:publish --tag=spa-vite-config --force
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


Add the middleware to app/bootstrapp/app.php
```bash
    use Illuminate\Session\Middleware\StartSession;
    ...
    ->withMiddleware(function (Middleware $middleware) {
        ...
      $middleware->statefulApi();
      $middleware->append(StartSession::class);
        ...
    })
```


Publish the config/cors.php file
```bash
php artisan config:publish cors
```

Change the config file
```bash
 'supports_credentials' => true,
```

Change this file resources/js/bootstrap.js
```bash
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
```
Change the .env file

```bash
SESSION_DOMAIN='.localhost'
```

Add the Throttle-functionality to app/Prividers/AppServiceProvider
You can change the values
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
        return Limit::perMinute(config('spa.global_throttle', 1000));
    });
}
```

Make your necessary changes in .env and config/app.php


Add a user with following command:
```bash
    php artisan user:create
```


## Usage
```bash
   php artisan serve
   npm run dev
   php artisan queue:work
```

## Testingx

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
