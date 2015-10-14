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

$rules = [
    'User.Authentication' => '/users/auth',
    'User.Users'          => '/users/{id?}'
];

/** @var \App\Restful\RouteRuleBuilder $router */
$router = $app->make('restful.route_ruler_builder');

$router->setPrefix('api')->setVersion('v1')->setBaseNamespace('App.Http.Api')
    ->mappingFromRules($rules)
    ->withCors()
    ->withAuth()
    ->buildToApp($app);
