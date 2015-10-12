<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

class ActionResultBuilder
{

    /**
     * @var \App\Restful\ActionResult
     */
    private $_result;

    /**
     * @return ActionResultBuilder
     */
    public function __construct()
    {
        $this->_result = new ActionResult();
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->_result->headers[$key] = $value;
        return $this;
    }

    /**
     * @param $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->_result->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setResource($data)
    {
        $this->_result->resource = $data;
        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->_result->message = $message;
        return $this;
    }


    /**
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        return $this->setMessage(implode("\n", $messages));
    }

    /**
     * @return \App\Restful\ActionResult
     */
    public function build()
    {
        return $this->_result;
    }
}