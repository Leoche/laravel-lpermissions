
# Leoche/Laravel-LPermissions

[![Laravel](https://img.shields.io/badge/Laravel-5.3-red.svg?style=flat)](https://laravel.com/docs/5.3)
[![Source](https://img.shields.io/badge/Source-leoche/lpermissions-green.svg?style=flat)](https://github.com/leoche/laravel-lpermissions/)
[![License](http://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat)](https://tldrlegal.com/license/mit-license)

Laravel LPermissions adds roles and permissions to Auth Laravel 5.3. Protect your routes and your views.

### Table of Contents
* [Requirements](#requirements)
* [Installation](#installation)
* [Routes Usage](#routes)
* [Blades Usage](#blades)
* [Example](#example)


### <a name="requirements"></a>Requirements

* This package requires PHP 5.5+
* This package requires Laravel 5.3

## <a name="installation"></a>Installation

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
## <a name="routes"></a>Routes Usage

You just have to specifythe middleware to the group route. It will check for permission and abort 401 if unauthorised
```php
Route::get('/home', function () {
	return "You can go here";
});
...
Route::group(['middleware' => ['auth']], function () {
	Route::get('/home1', function () {
		return "You can go here if you're logged";
	});
});
...
Route::group(['middleware' => ['permission']], function () {
	Route::get('/home2', function () {
		return "You can go here if you or your role have '/home2' or '/*' permission";
	});
});
...
Route::group(['middleware' => ['auth','permission']], function () {
	Route::get('/home3', function () {
		return "You can go here if you're logged and you or your role have '/home3' or '/*' permission";
	});
});
```

## <a name="blades"></a>Blades Usage

In your blades view you can use directives to show something (eg: links, infos) only if the user has the permission or the role
```php
@permission('admin/dashboard')
 //Only shown to users who can access to admin dashboard
@endpermission
...
@role('moderator')
 //Only shown to moderators role
@endrole
```

## <a name="example"></a>Example

Users Table

| id | username |role_id|
| -- |--------- |-------|
| 1  | Mike     |   0   |
| 2  | Lisa     |   1   |
| 3  | John     |   2   |

Roles Table

| id | name    |
| -- |-------- |
| 1  | Admin   |
| 2  | Member  |


Permissions Table

| id | route      | method | user_id | role_id |
| -- |----------- |--------|---------|---------|
| 1  | /admin/*   |   *    |    0    |    1    |
| 2  | /account/* |   GET  |    0    |    2    |
| 3  | /secret    |   GET  |    1    |    0    |

Route web.php

```php
Route::get('/', function () {
	return "home ppage";
});

Route::group(['middleware' => ['auth','permission']], function () {
	Route::get('/secret', function () {
		return "SECRET PAGE";
	});
	Route::get('/account', function ($id) {
		return "view account infos";
	});
});

Route::group(["prefix" => "admin",'middleware' => ['auth','permission']], function () {
	Route::get('/', function () {
		return view('dashboard');
	});
	Route::ressource('posts', 'PostController');
});
```

Everyone can see the homepage

Only mike can view /secret

Lisa can do anything in /admin/* (Ex: POST /admin/posts)

John can only view accounts pages