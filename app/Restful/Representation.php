<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Symfony\Component\HttpFoundation\Response;

class Representation
{
    /**
     * @var \Illuminate\Http\Response
     */
    protected $response;

    private $_content;

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * set http header
     *
     * @param string    $key
     * @param string    $value
     * @param bool|true $replace
     *
     * @return $this
     */
    public function setHeader($key, $value, $replace = true)
    {
        $this->response->header($key, $value, $replace);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * @param      $statusCode
     * @param null $text
     *
     * @return $this
     */
    public function setStatusCode($statusCode, $text = null)
    {
        $this->response->setStatusCode($statusCode, $text);
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

}