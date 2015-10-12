<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

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
     * @var mixed
     */
    public $resourceId;

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

}