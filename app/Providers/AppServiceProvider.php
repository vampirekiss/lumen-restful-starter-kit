<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Restful\Security\IAuthBundle;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IAuthBundle::class, function () {
            return new \App\Http\Auth\ClientAuthBundle;
        });
    }

}
