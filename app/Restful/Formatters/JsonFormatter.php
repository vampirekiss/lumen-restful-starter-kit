<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Formatters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Restful\ActionResult;
use App\Restful\IFormatter;
use App\Restful\RestfulRequest;


class JsonFormatter implements IFormatter
{

    /**
     * @var \App\Restful\RestfulRequest
     */
    private $_currentRestfulRequest;

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Restful\RestfulRequest
     */
    public function formatRequest(Request $request)
    {
        $this->_currentRestfulRequest = RestfulRequest::createFromRequest($request);
        return $this->_currentRestfulRequest;
    }

    /**
     * @param \App\Restful\ActionResult $result
     *
     * @return \Illuminate\Http\Response
     */
    public function formatActionResult(ActionResult $result)
    {
        $message = $result->message ? $result->message : Response::$statusTexts[$result->statusCode];

        $value = [
            'code'    => $result->statusCode,
            'data'    => $this->_morphToArray($result->resource),
            'message' => $message,
        ];

        $headers = array_merge(
            $result->headers, ['Content-Type' => 'application/json; charset=utf-8']
        );

        $response = Response::create(json_encode($value), $result->statusCode, $headers);

        return $response;
    }

    /**
     * @param mixed $resource
     *
     * @return mixed
     */
    private function _morphToArray($resource)
    {
        $array = call_user_func(function() use ($resource) {
            if (is_array($resource)) {
                return $resource;
            } elseif ($resource instanceof Arrayable) {
                return $resource->toArray();
            } elseif ($resource instanceof \JsonSerializable) {
                return $resource->jsonSerialize();
            }
            return $resource;
        });

        if (!is_array($array)) {
            return $resource;
        }

        return $array;
    }

}