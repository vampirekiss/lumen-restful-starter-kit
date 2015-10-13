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


/** @var \App\Restful\RouteRuleBuilder $builder */
$builder = $app->make('restful.route_ruler_builder');

$builder->setPrefix('api')->setVersion('v1')->setBaseNamespace('App.Http.Api');

$builder->mappingFromArray([

    'User.Authentication' => '/users/auth',
    'User.Users'          => '/users/{id?}'

]);

$builder->buildToApp($app);