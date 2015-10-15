<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Auth;

use Illuminate\Http\Response;
use App\Models\Client;
use App\Models\Token;
use App\Restful\Exceptions\RestfulException;
use App\Restful\RestfulRequest;
use App\Restful\Security\Credential;
use App\Restful\Security\IAuthBundle;


class ClientAuthBundle implements IAuthBundle
{
    /**
     * @var \App\Models\Token
     */
    private $_token;

    /**
     * @var \App\Models\Client
     */
    private $_client;

    /**
     * authenticate
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\Security\Credential
     */
    public function authenticate(RestfulRequest $request)
    {
        $clientId = $request->input->get('client_id');

        if (!$clientId) {
            throw new RestfulException(Response::HTTP_UNPROCESSABLE_ENTITY, 'missing client_id');
        }

        $this->_client = Client::enabled($clientId)->first();

        if (!$this->_client) {
            throw new RestfulException(Response::HTTP_UNPROCESSABLE_ENTITY, 'invalid client_id');
        }

        $this->_token = Token::updateOrCreate([
            'client_id' => $clientId,
            'uid' => $request->input->get('username')
        ], [
            'value' => Token::uniqueToken(),
            'expires_at' => $this->_client->getTokenExpiresAt()
        ]);

        return new Credential(
            $this->_token->getAttribute('value'),
            max($this->_token->getAttribute('expires_at') - time(), 0)
        );
    }

    /**
     * validate token
     *
     * @param string $token
     *
     * @return bool
     */
    public function validateToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $this->_token = Token::ofToken($token)->first();

        if ($this->_token == null || $this->_token->isExpired()) {
            throw new RestfulException(Response::HTTP_UNAUTHORIZED, "invalid token");
        }

        $this->_client = Client::enabled($this->_token->getAttribute('client_id'))->first();

        return $this->_client != null;
    }

    /**
     * validate request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function validateRequest(RestfulRequest $request)
    {
        if (!$this->_client) {
            return false;
        }

        $signature = $request->input->get('signature');
        if (!$signature) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'missing signature');
        }

        $params = $request->input->all();
        $params['token'] = $this->_token->getAttribute('value');
        unset($params['signature']);
        ksort($params);
        
        $clientSignature = sha1($this->_client->getAttribute('security') . implode(',' , $params));
        if ($signature != $clientSignature) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'invalid signature ' . $clientSignature);
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
    public function hasRights($request)
    {
        return true;
    }

}