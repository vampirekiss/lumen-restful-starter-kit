<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

use App\Restful\Security\IAuthBundle;
use App\Restful\RestfulRequest;
use App\Restful\Security\Credential;

class DummyAuthBundle implements IAuthBundle
{
    public static $token = '123456';

    public function authenticate(RestfulRequest $request)
    {
        return new Credential(self::$token);
    }

    public function isAuthorized(RestfulRequest $request)
    {
        return $request->token == self::$token;
    }

    public function hasRights(RestfulRequest $request)
    {
        return true;
    }

}

/**
 * @param \Illuminate\Container\Container|\Laravel\Lumen\Application $app
 */
function rebind($app)
{
    unset($app[IAuthBundle::class]);
    $app->bind(IAuthBundle::class, DummyAuthBundle::class);
}

