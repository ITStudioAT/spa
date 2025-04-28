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
- Login page 
- Unknown password page
- Register page
- Small Dashboard with
    - Logout


## Installation
First install a new Laravel app
```bash
laravel new new-laravel-app
```

**Make the correct configurations in**
- .env
    - APP_URL
    - DB-configuration
    - MAIL-configuration
    - SESSION_DOMAIN='.localhost'
    - LOCALE-configuration
    
- config/app.php
    - timezone
   

**Create a new empty database like in .env configured**

You can install the package via composer:
```bash
composer require itstudioat/spa
```

Run the Install-Command & the Complete-Command:
```bash
php artisan spa:install
php artisan spa:complete
```


Put this line in composer.json
```bash
    "autoload": {
        "psr-4": {
            ...
            "Itstudioat\\Spa\\": "src/"
        }
    },
```

Change the config/cors.php file
```bash
 'supports_credentials' => true,
```


Add to your Http/Controllers/Controller the HasRoleTrait:
```bash
  namespace App\Http\Controllers;
  use Itstudioat\Spa\Traits\HasRoleTrait;

  abstract class Controller
  {
      use HasRoleTrait;
```

If you use Postmark for Mailing (i use it):
```bash
composer require symfony/postmark-mailer
composer require symfony/http-client
```

To inividualize the Mail-Logo, publish the Markdown files to resources/views/vendor/mail
```bash
php artisan vendor:publish --tag=laravel-mail
```

After that change the file resources/views/mail/html/header.blade.php
```bash
@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('storage/images/logo.png') }}" class="logo" alt="Your Logo">
</a>
</td>
</tr>
```


**Comment the standard route out in routes/web.php**
```bash
/*
Route::get('/', function () {
    return view('welcome');
});
*/
```

## Usage
```bash
   php artisan serve
   npm run dev
   php artisan queue:work
```

## Permissions for routes ##
### Web-Routes ###
Under routes/meta/web there is for each route.js-file a php-file.
Here you may define, which routes need which (spatie-)roles.
if the array is empty, no permission is needed.

With a simple command you can synchronize these files.
The php-file is made actual with the route-js-file as basis:
```bash
    php artisan routes:sync
```

### Api-Routes ###
Under routes/meta/api there must exist a file for each api-route.
If you have following api-routes: 
/admin/config
/routes/check
You must have two files:
admin.php
routes.php

There you may define routes, which are protected with roles.



### Sending E-Mails ###
That everything works fine, sending E-Mails must be configured.
I use Postmark for Mailing and everything is fine.
You can find more infos under Laravel/Notifications

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
