<?php

namespace App\Providers;

use App\Http\Api\Handler;
use App\Restful\Services\CrosManager;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CrosManager::class, function() {
            $crosManager = new CrosManager();
            $crosManager->allowOrigin('*')
                ->allowHeaders(
                    [
                       'Authorization', 'Content-Type', 'If-Match',
                       'If-Modified-Since', 'If-None-Match', 'If-Unmodified-Since'
                    ]
                )->allowMethods(Handler::$availableMethods)
                ->exposeMethods(Handler::$availableMethods)
                ->maxAge(86000)
                ->allowCredentials(true);
            return $crosManager;
        });
    }

}
