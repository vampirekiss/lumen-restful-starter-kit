<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestfulRequest
{
    /**
     * @var string
     */
    public $apiClass;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $method;

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $query;

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
     * jsonp callback
     *
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


        if ($request->query->has('per_page')) {
            $instance->perPage = intval($request->query->get('per_page'));
        }

        if ($request->query->has('page')) {
            $instance->page = intval($request->query->get('page'));
        }

        $instance->query = $request->query;
        $instance->apiClass = explode('@', $request->route()[1]['uses'])[0];
        $instance->method = $request->getMethod();
        $instance->headers = $request->headers;
        $instance->token = static::getToken($request);
        $instance->callback = $request->query->get('callback');

        $params = $request->route()[2];
        if (isset($params['id'])) {
            $id = intval($params['id']);
            $instance->resourceId = $id > 0 ? $id : null;
        }

        return $instance;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public static function getToken(Request $request)
    {
        if ($request->query->has('token')) {
            return $request->query->get('token');
        }

        $authHeader = strtolower($request->header('Authorization'));
        if ($authHeader && Str::contains($authHeader, 'token ')) {
            return str_replace('token ', '', $authHeader);
        }
        return '';
    }
}