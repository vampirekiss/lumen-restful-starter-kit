<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Middleware;

use Illuminate\Http\Response;

class CorsMiddleware
{
    /**
     * @var array
     */
    public static $corsHeaders = [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-GitHub-OTP, X-Requested-With',
        'Access-Control-Allow-Methods'     => 'GET, HEAD, POST, PATCH, PUT, DELETE',
        'Access-Control-Expose-Headers'    => 'ETag, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset',
        'Access-Control-Max-Age'           => '86400',
        'Access-Control-Allow-Credentials' => 'true'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, \Closure $next)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return response('', Response::HTTP_NO_CONTENT, static::$corsHeaders);
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($response->getStatusCode() >= 400) {
            foreach (static::$corsHeaders as $key => $value) {
                $response->header($key, $value);
            }
        }

        return $response;
    }

}