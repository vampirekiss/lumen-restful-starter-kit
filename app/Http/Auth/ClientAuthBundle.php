<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Auth;

use App\Http\Mixin\GetClient;
use Illuminate\Http\Response;
use App\Models\Token;
use App\Restful\Exceptions\RestfulException;
use App\Restful\RestfulRequest;
use App\Restful\Security\Credential;
use App\Restful\Security\IAuthBundle;


class ClientAuthBundle implements IAuthBundle
{
    use GetClient;

    /**
     * authenticate
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\Security\Credential
     */
    public function authenticate(RestfulRequest $request)
    {
        $client = $this->getClientOrFail($request);

        $secret = $request->query->get('secret');
        if (!$secret) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'missing secret');
        }

        if ($secret != $client->getAttribute('secret')) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'invalid secret');
        }

        /** @var \App\Models\Token $token */
        $token = Token::ofToken($request->token)->firstOrNew([
            'client_id' => $client->getAttribute('id'),
            'token' => Token::uniqueToken(),
            'expires_in' => intval($client->getAttribute('expires_in'))
        ]);

        return new Credential(
            $token->getAttribute('token'), $token->getAttribute('expires_in')
        );
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function isAuthorized(RestfulRequest $request)
    {
        if (!$request->token) {
            throw new RestfulException(Response::HTTP_UNAUTHORIZED, "missing token");
        }

        /** @var \App\Models\Token $tokenModel */
        $tokenModel = Token::ofToken($request->token)->first();

        if ($tokenModel == null || $tokenModel->isExpired()) {
            throw new RestfulException(Response::HTTP_UNAUTHORIZED, "token is invalid or expired");
        }

        return true;
    }

    /**
     * has rights to access api
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function hasRights(RestfulRequest $request)
    {
        return true;
    }

}
