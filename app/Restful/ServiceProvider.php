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
        $this->app->singleton('restful.formatter', function() {
            return new JsonFormatter();
        });

        $this->app->bind('restful.repository', function ($app, $params) {
            return new ModelRepository($params[0]);
        });

        $this->app->bind('restful.route_ruler_builder', function() {
            return new RouteRuleBuilder();
        });

        $this->app->singleton('restful.handlers.document', function() {
            return new ActionHandlers\DocumentHandler();
        });

        $this->app->singleton('restful.handlers.collection', function() {
            return new ActionHandlers\CollectionHandler();
        });
    }

}