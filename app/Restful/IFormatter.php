<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;

interface IFormatter
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Restful\RestfulRequest
     */
    public function formatRequest(Request $request);

    /**
     * @param \App\Restful\ActionResult $result
     *
     * @return \Illuminate\Http\Response
     */
    public function formatActionResult(ActionResult $result);

}