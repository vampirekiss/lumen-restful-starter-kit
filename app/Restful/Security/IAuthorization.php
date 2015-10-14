<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;

interface IAuthorization
{

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
     * @param string $apiClass
     * @param string $requestMethod
     *
     * @return bool
     */
    public function hasRights($apiClass, $requestMethod);
}