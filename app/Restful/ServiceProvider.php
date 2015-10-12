<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use App\Restful\Formatters\JsonFormatter;
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
        $this->app->singleton(IFormatter::class, function() {
            return new JsonFormatter();
        });

        $this->app->singleton('restful.handlers.document', function() {
            return new ActionHandlers\DocumentHandler();
        });

        $this->app->singleton('restful.handlers.collection', function() {
            return new ActionHandlers\CollectionHandler();
        });
    }

}