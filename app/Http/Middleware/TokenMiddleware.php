<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->input('token');

        if (!$this->_isValidToken($token)) {
            return response('', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    /**
     * token is valid
     *
     * @param string $token
     *
     * @return mixed
     */
    private function _isValidToken($token)
    {
        return $token;
    }
}
