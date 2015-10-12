<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use App\Restful\Formatters\JsonFormatter;
use App\Restful\Repositories\ModelRepository;
use App\Restful\ActionHandlers;


class ServiceProvider extends BaseServiceProvider
{
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

        $this->app->singleton('restful.handlers.document', function() {
            return new ActionHandlers\DocumentHandler();
        });

        $this->app->singleton('restful.handlers.collection', function() {
            return new ActionHandlers\CollectionHandler();
        });
    }

}