# Lumen restful starter kit

Lumen restful starter kit is a restful api kit based on [Lumen](https://github.com/laravel/lumen)

## Features

* Mapping Http Verbs as Model CURD Operations
* Model CURD event handling
* Token Authentication
* Extendable Validation Rules
* CORS
* Response Formatters
* Api & TestCase Command

## Create a new api

```
php artisan make:api User.Users -m -t
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

