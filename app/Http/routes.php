<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Application;

$app->group(['prefix' => 'api/v1'], function (Application $app) {

    $routeRules = [
        'User.User' => '/users'
    ];

    foreach ($routeRules as $handler => $uri) {
        $handlerClass = str_replace('.', '\\', sprintf('App.Http.Api.%s', $handler));

        foreach ($handlerClass::$availableMethods as $method) {
            $action = sprintf('%s@handle', $handlerClass);
            $app->addRoute($method, $uri, $action);
            $app->addRoute($method, $uri . '/{id}', $action);
        }

    }

});


