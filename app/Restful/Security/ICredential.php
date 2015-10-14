<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;


interface ICredential
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * token expires in
     *
     * @return int
     */
    public function getTokenExpiresIn();

    /**
     * get user info
     *
     * @return array
     */
    public function getUserInfo();


}