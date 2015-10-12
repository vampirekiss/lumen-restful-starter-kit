<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;

interface IHttpHandler
{

    /**
     * handle http request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function handleRequest(Request $request);

}