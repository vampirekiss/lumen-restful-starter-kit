<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use App\Restful\Services\CrosManager;
use Illuminate\Http\Response;

class Document extends ResourceAction
{

    /**
     * @param \App\Restful\Request $request
     *
     * @return mixed
     */
    public function get(Request $request)
    {
        $resource = $this->repository->retrieve($request->resourceId);
        $this->representation->setContent($resource)
            ->setContent($resource);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    public function head(Request $request)
    {
        $resource = $this->get($request);
        if (!$resource) {
            $this->representation->setStatusCode(Response::HTTP_NOT_FOUND);
        }
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
     * @return mixed
     */
    protected function post(Request $request)
    {
        $allowedMethods = [
            self::GET, self::PUT, self::PATCH, self::DELETE, self::HEAD, self::OPTIONS
        ];
        $this->representation->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
            ->setHeader('Allow', implode(', ', $allowedMethods ));
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    public function put(Request $request)
    {
        $result = $this->repository->remove(
            [
                new Filter('id', '=', $request->resourceId)
            ]
        );

        if (!$result) {
            $this->representation->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $resource = $this->repository->create($request->input->all(), $request->resourceId);
        $this->representation->setStatusCode(Response::HTTP_OK)
            ->setContent($resource);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    public function patch(Request $request)
    {
        $resource = $this->repository->update($request->resourceId, $request->input->all());

        if ($resource) {
            $this->representation->setStatusCode(Response::HTTP_OK)
                ->setContent($resource);
            return;
        }

        $this->representation->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected function delete(Request $request)
    {
        $result = $this->repository->remove([
            new Filter('id', '=', $request->resourceId)
        ]);

        if ($result) {
            $this->representation->setStatusCode(Response::HTTP_NO_CONTENT);
            return;
        }

        $this->representation->setStatusCode(Response::HTTP_NOT_FOUND);
    }

}