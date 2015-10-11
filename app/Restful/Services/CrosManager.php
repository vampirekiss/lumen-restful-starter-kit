<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Services;

class CrosManager
{
    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @param $origin
     *
     * @return $this
     */
    public function allowOrigin($origin)
    {
        $this->_headers['Access-Control-Allow-Origin'] = $origin;
        return $this;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function allowHeaders(array $headers)
    {
        $this->_headers['Access-Control-Allow-Headers'] = $this->_arrayHeaderValue($headers);
        return $this;
    }

    /**
     * @param array $methods
     *
     * @return $this
     */
    public function allowMethods(array $methods)
    {
        $this->_headers['Access-Control-Allow-Methods'] = $this->_arrayHeaderValue($methods);
        return $this;
    }

    /**
     * @param array $methods
     *
     * @return $this
     */
    public function exposeMethods(array $methods)
    {
        $this->_headers['Access-Control-Expose-Headers'] = $this->_arrayHeaderValue($methods);
        return $this;
    }

    /**
     * @param string $maxAge max age
     *
     * @return $this
     */
    public function maxAge($maxAge)
    {
        $this->_headers['Access-Control-Max-Age'] = $maxAge;
        return $this;
    }

    /**
     * @param bool $allow
     *
     * @return $this
     */
    public function allowCredentials($allow)
    {
        $this->_headers['Access-Control-Allow-Credentials'] = $allow ? 'true' : 'false';
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_headers)) {
            return $this->_headers[$key];
        }
        return null;
    }

    /**
     * @param array $headers
     *
     * @return string
     */
    private function _arrayHeaderValue(array $headers)
    {
        return implode(', ', $headers);
    }

}