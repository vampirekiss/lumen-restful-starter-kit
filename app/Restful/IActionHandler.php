<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

interface IActionHandler
{

    /**
     * handle request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return ActionResult
     */
    public function handle(RestfulRequest $request);

}