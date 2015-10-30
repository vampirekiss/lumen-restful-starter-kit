<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\ActionHandlers;

use App\Restful\ActionHandler;
use App\Restful\IRepositoryAware;
use App\Restful\RepositoryAwareTrait;
use App\Restful\RestfulRequest;
use Illuminate\Http\Response;

class DocumentHandler extends ActionHandler implements IRepositoryAware
{
    use RepositoryAwareTrait;

    /**
     * should validate request
     *
     * @param \App\Restful\RestfulRequest $request
     *
     * @return bool
     */
    public function shouldValidateRequest(RestfulRequest $request)
    {
        return in_array($request->method, ['PUT', 'PATCH']);
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function get(RestfulRequest $request)
    {
        $resource = $this->getRepository()->retrieve($request->resourceId);

        if (!$resource) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_NOT_FOUND)
                ->build();
        }

        return $this->actionResultBuilder()
            ->setResource($resource)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function head(RestfulRequest $request)
    {
        return $this->get($request);
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function put(RestfulRequest $request)
    {
        $resource = $this->getRepository()
            ->replace($request->resourceId, $request->input->all());

        if (!$resource) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->build();
        }

        return $this->actionResultBuilder()
            ->setResource($resource)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function patch(RestfulRequest $request)
    {
        $resource = $this->getRepository()->update($request->resourceId, $request->input->all());

        if (!$resource) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_NOT_FOUND)
                ->build();
        }

        return $this->actionResultBuilder()
            ->setResource($resource)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function delete(RestfulRequest $request)
    {
        if (!$this->getRepository()->remove($request->resourceId)) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_NOT_FOUND)
                ->build();
        }

        return $this->actionResultBuilder()
            ->setStatusCode(Response::HTTP_NO_CONTENT)
            ->build();
    }


}