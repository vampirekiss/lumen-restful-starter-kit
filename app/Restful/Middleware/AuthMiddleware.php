<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Middleware;

use App\Restful\Security\AuthHandler;


class AuthMiddleware
{

    /**
     * @var \App\Restful\Security\AuthHandler
     */
    protected $authHandler;

    /**
     * construct
     *
     * @param \App\Restful\Security\AuthHandler $authHandler
     *
     * @return AuthMiddleware
     */
    public function __construct(AuthHandler $authHandler)
    {
        $this->authHandler = $authHandler;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $authPath
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, \Closure $next, $authPath)
    {
        $response = $this->authHandler->handle($request, $authPath);

        if ($response) {
            return $response;
        }

        return $next($request);
    }

}