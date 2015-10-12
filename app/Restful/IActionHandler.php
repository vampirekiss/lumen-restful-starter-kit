<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

interface IActionHandler
{

    /**
     * should validate request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function shouldValidateRequest(RestfulRequest $request);

    /**
     * handle request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return ActionResult
     */
    public function handle(RestfulRequest $request);

}