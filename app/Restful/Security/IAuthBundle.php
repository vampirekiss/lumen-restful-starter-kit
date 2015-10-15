<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;

use App\Restful\RestfulRequest;


interface IAuthBundle
{
    /**
     * authenticate
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\Security\Credential
     */
    public function authenticate(RestfulRequest $request);


    /**
     * validate token
     *
     * @param string $token
     *
     * @return bool
     */
    public function validateToken($token);

    /**
     * has rights to access api
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function hasRights($request);

}