<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

class StatusTexts
{

    public static $humanTexts = [
        200 => 'Ok',
        201 => 'Create new resource succeeded',
        204 => 'The server has fulfilled the request but does not need to return an entity-body',
        400 => 'Problems parsing request data',
        401 => 'Requires user authentication',
        403 => 'Do not have permission to access this resource',
        404 => 'Resource not found',
        405 => 'Request method not allowed',
        422 => 'Request data validation failed',
        500 => 'Internal server error'
    ];

}