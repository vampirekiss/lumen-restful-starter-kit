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
     * is authorized
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function isAuthorized(RestfulRequest $request);


    /**
     * check has rights to access api after authorized
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function hasRights(RestfulRequest $request);

}