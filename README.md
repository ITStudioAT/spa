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
    - (REDIS_CLIENT=phpredis to REDIS_CLIENT=predis)
    
- config/app.php
    - timezone
    - locale-configuration

**Create a new empty database like in .env configured**
Migrate your mogrations
```bash
php artisan migrate
```

You can install the package via composer:
```bash
composer require itstudioat/spa
```

But this in the composer.json
```bash
    "autoload": {
        "psr-4": {
            ...
            "Itstudioat\\Spa\\": "src/"
        }

```

Publish all ressources of Laravel-Spa
```bash
php artisan vendor:publish --tag=spa-all --force
php artisan vendor:publish --tag=spa-migrations
php artisan migrate
```

Publishing Spatie-Permission
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan optimize:clear
php artisan migrate
```

To install the laravel sanctum and when you are asked, lets run the migrations:
```bash
php artisan install:api
```


To integrate all packages like vue:
```bash
php artisan install:me
npm install
```

Make storage:link and create  /storage/app/public/images folder
put the favicon.ico and logo.png in this folder
```bash
php artisan storage:link
```

Publish the config/cors.php file
```bash
php artisan config:publish cors
```

Change the config/cors.php file
```bash
 'supports_credentials' => true,
```

Add a user with following command:
```bash
    php artisan user:create
```

If you use Postmark for Mailing (i use it):
```bash
composer require symfony/postmark-mailer
composer require symfony/http-client
```

To inividualize the Mail-Logo, pusblish de Markdown files to resources/views/vendor/mail
```bash
php artisan vendor:publish --tag=laravel-mail
```




## Usage
```bash
   php artisan serve
   npm run dev
   php artisan queue:work
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
