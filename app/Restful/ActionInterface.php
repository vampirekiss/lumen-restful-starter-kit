<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

interface ActionInterface
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';

    /**
     * handle request
     *
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    public function handle(Request $request);

}