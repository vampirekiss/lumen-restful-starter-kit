<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Restful\ActionResultBuilderTrait;

class RestfulException extends HttpException
{
    use ActionResultBuilderTrait;

    /**
     * @return \App\Restful\ActionResult
     */
    public function toActionResult()
    {
        return $this->actionResultBuilder()
            ->setStatusCode($this->getStatusCode())
            ->setHeaders($this->getHeaders())
            ->setMessage($this->getMessage())
            ->build();
    }

}