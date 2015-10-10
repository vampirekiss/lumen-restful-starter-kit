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

    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    $handlers = [
        'Users.User'
    ];

    foreach ($methods as $method) {
        foreach ($handlers as $handler) {
            $handlerClass = str_replace('.', '\\', sprintf('App.Http.Api.%s', $handler));
            if (class_exists($handlerClass)) {
                $action = sprintf('%s@handleRequest', $handlerClass);
                $app->addRoute($method, $handlerClass::$uri, $action);
                $app->addRoute($method, $handlerClass::$uri . '/{id}', $action);
            }
        }

    }

});


