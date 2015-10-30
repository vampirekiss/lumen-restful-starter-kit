<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Response;

class ActionResult
{
    /**
     * http status code
     *
     * @var int
     */
    public $statusCode = Response::HTTP_OK;

    /**
     * http headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * @var string|null
     */
    public $message = null;

    /**
     * resource
     *
     * @var mixed
     */
    public $resource = null;

}