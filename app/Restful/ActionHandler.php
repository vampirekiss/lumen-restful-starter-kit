<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Response;

abstract class ActionHandler implements IActionHandler
{
    use ActionResultBuilderTrait;

    /**
     * handle request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    public function handle(RestfulRequest $request)
    {
        $callable = [$this, $request->method];
        return call_user_func_array($callable, [$request]);
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function get(RestfulRequest $request)
    {
        return $this->_notAllowed();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function head(RestfulRequest $request)
    {
        return $this->actionResultBuilder()->setStatusCode(Response::HTTP_OK)->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function post(RestfulRequest $request)
    {
        return $this->_notAllowed();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function put(RestfulRequest $request)
    {
        return $this->_notAllowed();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function patch(RestfulRequest $request)
    {
        return $this->_notAllowed();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function delete(RestfulRequest $request)
    {
        return $this->_notAllowed();
    }

    /**
     * @return \App\Restful\ActionResult
     */
    private function _notAllowed()
    {
        return $this->actionResultBuilder()->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
            ->build();
    }

}