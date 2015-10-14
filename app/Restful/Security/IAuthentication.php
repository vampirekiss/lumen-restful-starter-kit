<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;

use App\Restful\RestfulRequest;

interface IAuthentication
{
    /**
     * authenticate
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return ICredential
     */
    public function authenticate(RestfulRequest $request);

}