<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;

class Credential
{

    /**
     * @var string
     */
    private $_token;

    /**
     * @var int
     */
    private $_tokenExpiresIn;


    /**
     * @param string $token
     * @param int    $tokenExpiresIn
     *
     * @return Credential
     */
    public function __construct($token, $tokenExpiresIn = 7200)
    {
        $this->_token = $token;
        $this->_tokenExpiresIn = $tokenExpiresIn;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @return int
     */
    public function getTokenExpiresIn()
    {
        return $this->_tokenExpiresIn;
    }

}