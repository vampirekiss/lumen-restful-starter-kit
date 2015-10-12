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
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function get(RestfulRequest $request)
    {
        $data = $this->getRepository()->queryWithParams($request->input->all());

        return $this->actionResultBuilder()->setData($data)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function post(RestfulRequest $request)
    {
        $data = $this->getRepository()->create($request->input->all());

        return $this->actionResultBuilder()->setStatusCode(Response::HTTP_CREATED)
            ->setData($data)
            ->build();
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function delete(RestfulRequest $request)
    {
        $this->getRepository()->removeWithParams($request->input->all());

        return $this->actionResultBuilder()->setStatusCode(Response::HTTP_NO_CONTENT)
            ->build();
    }


}
