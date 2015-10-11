<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Symfony\Component\HttpFoundation\ParameterBag;

class Request
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var ParameterBag
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
    public $pageSize;

}