<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use App\Restful\Services\CrosManager;
use Illuminate\Http\Response;

class Collection extends ResourceAction
{

    /**
     * @var array
     */
    private $_allowedMethods = [
        self::GET, self::POST, self::DELETE, self::OPTIONS, self::HEAD
    ];

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function get(Request $request)
    {
        $resources = $this->repository->queryWithParams($request->input->all());
        $this->representation->setStatusCode(Response::HTTP_OK)
            ->setContent($resources);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function head(Request $request)
    {
        $this->representation->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function options(Request $request)
    {
        /** @var CrosManager $crosBuilder */
        $crosBuilder = $this->getService(CrosManager::class);
        foreach ($crosBuilder->getHeaders() as $key => $value) {
            $this->representation->setHeader($key, $value);
        }
        $this->representation->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function post(Request $request)
    {
        $resource = $this->repository->create($request->input->all());
        $this->representation->setStatusCode(Response::HTTP_CREATED)
            ->setContent($resource);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function put(Request $request)
    {
        $this->representation->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
            ->setHeader('Allow', implode(',', $this->_allowedMethods));
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function patch(Request $request)
    {
        $this->representation->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
            ->setHeader('Allow', implode(',', $this->_allowedMethods));
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function delete(Request $request)
    {
        $this->repository->removeWithParams($request->input->all());
        $this->representation->setStatusCode(Response::HTTP_NOT_FOUND);
    }

}
