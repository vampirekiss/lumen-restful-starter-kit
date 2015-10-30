<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use App\Restful\Formatters\JsonFormatter;
use App\Restful\ActionHandlers;
use App\Restful\Repositories\ModelRepository;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var \Illuminate\Validation\Factory $validatorFactory */
        $validatorFactory = $this->app->make('validator');
        $validatorFactory->resolver(function () {
            return (new \ReflectionClass(Validator::class))->newInstanceArgs(func_get_args());
        });
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IFormatter::class, function () {
            return new JsonFormatter();
        });

        $this->app->bind(IRepository::class, function ($app, $params) {
            return new ModelRepository($params[0]);
        });

        $this->app->bind(RouteRuleBuilder::class, function () {
            return new RouteRuleBuilder();
        });

        $this->app->bind(ActionHandlers\DocumentHandler::class, function () {
            return new ActionHandlers\DocumentHandler();
        });

        $this->app->bind(ActionHandlers\CollectionHandler::class, function () {
            return new ActionHandlers\CollectionHandler();
        });
    }

}