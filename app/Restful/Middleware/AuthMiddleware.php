<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Middleware;

use Illuminate\Http\Response;
use App\Restful\ActionResultBuilderTrait;
use App\Restful\Security\IAuthentication;
use App\Restful\Security\IAuthorization;


class AuthMiddleware
{
    use ActionResultBuilderTrait;

    /**
     * @var \App\Restful\IFormatter
     */
    private $_formatter;

    /**
     * construct
     *
     * @return AuthMiddleware
     */
    public function __construct()
    {
        $this->_formatter = app()->make('restful.formatter');
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
        $restfulRequest = $this->_formatter->formatRequest($request);

        if (strtolower($authPath) == strtolower($request->path())) {
            /** @var \App\Restful\Security\IAuthentication $authentication */
            $authentication = app()->make(IAuthentication::class);
            $credential = $authentication->authenticate($restfulRequest);
            if (!$credential) {
                return $this->_unauthorizedResponse();
            }

            return $this->actionResultBuilder()->setResource([
                'access_token' => $credential->getToken(),
                'expires_in'   => $credential->getTokenExpiresIn(),
                'user_info'    => $credential->getUserInfo()
            ])->build();
        }

        if (!$restfulRequest->token) {
            return $this->_unauthorizedResponse();
        }

        /** @var \App\Restful\Security\IAuthorization $authorization */
        $authorization = app()->make(IAuthorization::class);

        if (!$authorization->validateToken($restfulRequest->token)) {
            return $this->_unauthorizedResponse();
        }

        if (!$authorization->hasRights($restfulRequest->apiClass, $restfulRequest->method)) {
            return $this->_forbiddenResponse();
        }

        return $next($request);
    }


    /**
     * @return \Illuminate\Http\Response
     */
    private function _unauthorizedResponse()
    {
        return $this->_formatter->formatActionResult(
            $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNAUTHORIZED)
                ->build()
        );
    }

    /**
     * @return \Illuminate\Http\Response
     */
    private function _forbiddenResponse()
    {
        return $this->_formatter->formatActionResult(
            $this->actionResultBuilder()->setStatusCode(Response::HTTP_FORBIDDEN)
                ->build()
        );
    }
}