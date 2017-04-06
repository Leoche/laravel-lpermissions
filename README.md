
# Leoche/Laravel-LPermissions

[![Laravel](https://img.shields.io/badge/Laravel-5.3-red.svg?style=flat)](https://laravel.com/docs/5.3)
[![Source](https://img.shields.io/badge/Source-leoche/lpermissions-green.svg?style=flat)](https://github.com/leoche/laravel-lpermissions/)
[![License](http://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat)](https://tldrlegal.com/license/mit-license)

Laravel LPermissions adds roles and permissions to Auth Laravel 5.3. Protect your routes and your views.

# Table of Contents
* [Requirements](#requirements)
* [Installation](#installation)
* [Routes Usage](#routes)
* [Blades Usage](#blades)


# <a name="requirements"></a>Requirements

* This package requires PHP 5.5+
* This package requires Laravel 5.3

# <a name="installation"></a>Installation

1. Require the package in your `composer.json` and update your dependency with `composer update`:

```
"require": {
...
"leoche/laravel-lpermissions": "~1.0",
...
},
```

2. Add the package to your application service providers in `config/app.php`.

```php
'providers' => [

'Illuminate\Foundation\Providers\ArtisanServiceProvider',
'Illuminate\Auth\AuthServiceProvider',
...
'Leoche\LPermissions\LPermissionsServiceProvider',

],
```

3. Publish the package migrations to your application and run these with `php artisan migrate.

```
$ php artisan vendor:publish --provider="Leoche\LPermissions\LPermissionsServiceProvider"
```

4. Add the middleware to your `app/Http/Kernel.php`.

```php
protected $routeMiddleware = [

....
'permission' => 'Leoche\LPermissions\Middleware\checkPermission',

];
```

5. Add the HasRole trait to your `User` model.

```php
use Leoche\LPermissions\Traits\HasRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
use Authenticatable, HasRole;
}
```
# <a name="routes"></a>Routes Usage

Todo

# <a name="blades"></a>Blades Usage

Todo