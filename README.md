# Lumen restful starter kit

Lumen restful starter kit is a restful api kit base on [Lumen](https://github.com/laravel/lumen)

## Create a new api

```
php artisan make:api Product.Products -m -t
```

## Route rule

```
// app/Http/routes.php

$rules = [
    // start with @ means it is a public api
    // {id?} means mapping '/users', '/users/1234' to User.Users
    '@User.Users'          => '/users/{id?}',
    'Clients'             => '/clients/{id?}'
];
```