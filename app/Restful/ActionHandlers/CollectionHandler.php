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


class CollectionHandler extends ActionHandler implements IRepositoryAware
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
        return in_array($request->method, ['POST']);
    }


    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function get(RestfulRequest $request)
    {
        $resource = $this->getRepository()->paginateByParams(
            $request->input, $request->page, $request->perPage
        );

        return $this->actionResultBuilder()
            ->setResource($resource)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function post(RestfulRequest $request)
    {
        $resource = $this->getRepository()->create($request->input->all());

        return $this->actionResultBuilder()
            ->setStatusCode(Response::HTTP_CREATED)
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
        $this->getRepository()->removeByParams($request->input);

        return $this->actionResultBuilder()
            ->setStatusCode(Response::HTTP_NO_CONTENT)
            ->build();
    }


}
