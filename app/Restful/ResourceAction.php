<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

abstract class ResourceAction implements ActionInterface
{

    /**
     * @var \App\Restful\Repository
     */
    protected $repository;

    /**
     * @var \App\Restful\Representation
     */
    protected $representation;

    /**
     * @param \App\Restful\Repository $repository
     * @param Representation                   $representation
     *
     * @return ResourceAction
     */
    public function __construct(Repository $repository, Representation $representation)
    {
        $this->repository = $repository;
        $this->representation = $representation;
    }

    /**
     * handle request
     *
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    public function handle(Request $request)
    {
        $callable = [$this, $request->method];
        call_user_func_array($callable, [$request]);
    }

    /**
     * get service
     *
     * @param $class
     *
     * @return object
     */
    protected function getService($class)
    {
        return app()->make($class);
    }

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function get(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function head(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function options(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function post(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function put(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function patch(Request $request);

    /**
     * @param \App\Restful\Request $request
     *
     * @return void
     */
    protected abstract function delete(Request $request);



}