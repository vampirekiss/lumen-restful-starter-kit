<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;

class RestfulRequest
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $input;

    /**
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    public $headers;

    /**
     * @var mixed
     */
    public $resourceId = 0;

    /**
     * @var string
     */
    public $callback;

    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $perPage;

    // todo sort, jsonp


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return static
     */
    public static function createFromRequest(Request $request)
    {
        $instance = new static();

        if (in_array($request->getMethod(), ['POST', 'PATCH', 'PUT'])) {
            $instance->input = $request->json();
        } else {
            $instance->input = $request->query;
        }

        if ($request->query->has('per-page')) {
            $instance->perPage = intval($request->query->get('per-page'));
        }

        if ($request->query->has('page')) {
            $instance->page = intval($request->query->get('page'));
        }

        $instance->method = $request->getMethod();
        $instance->headers = $request->headers;

        $params = $request->route()[2];
        if (isset($params['id'])) {
            $id = intval($params['id']);
            $instance->resourceId = $id > 0 ? $id : null;
        }

        return $instance;
    }
}