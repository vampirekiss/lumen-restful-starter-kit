<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Mixin;

use App\Models\Client;
use App\Restful\Exceptions\RestfulException;
use App\Restful\RestfulRequest;
use Illuminate\Http\Response;

trait GetClient
{

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Models\Client
     */
    public function getClientOrFail(RestfulRequest $request)
    {
        $clientId = $request->query->get('client_id');
        if (!$clientId) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'missing client_id');
        }

        $client = Client::enabled($clientId)->first();
        if (!$client) {
            throw new RestfulException(Response::HTTP_BAD_REQUEST, 'invalid client');
        }

        return $client;
    }

}
