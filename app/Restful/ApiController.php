<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class ApiController extends Controller implements IHttpHandler, IRepositoryAware
{
    use ActionResultBuilderTrait, RepositoryAwareTrait;

    /**
     * @var array
     */
    public static $writeMethods = [
        'POST', 'PATCH', 'PUT', 'DELETE'
    ];

    /**
     * @var bool
     */
    protected $autoStartTransaction = true;

    /**
     * @var string
     */
    protected $resourceClass = '';

    /**
     * handle request
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function handleRequest(Request $request)
    {
        /** @var IFormatter $formatter */
        $formatter = $this->make('restful.formatter');

        $restfulRequest = $formatter->formatRequest($request);

        $actionHandler = $this->queryActionHandler($restfulRequest);

        if (!$actionHandler) {
            return $this->actionResultBuilder()->setStatusCode(Response::HTTP_NOT_FOUND)->build();
        }

        $actionResult = null;

        if ($actionHandler->shouldValidateRequest($restfulRequest)) {
            $validator = $this->validateRequest($restfulRequest);
            if ($validator && $validator->fails()) {
                $actionResult = $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessages($validator->getMessageBag()->all())
                    ->build();
            }
        }

        if ($actionResult === null) {
            if ($this->autoStartTransaction && in_array($request->getMethod(), self::$writeMethods)) {
                /** @var \Illuminate\Database\Connection $db */
                $db = $this->make('db');
                $actionResult = $db->transaction(function () use ($actionHandler, $restfulRequest) {
                    return $actionHandler->handle($restfulRequest);
                });
            } else {
                $actionResult = $actionHandler->handle($restfulRequest);
            }
        }

        return $formatter->formatActionResult($actionResult);
    }

    /**
     * query action handler
     *
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return IActionHandler
     */
    protected function queryActionHandler(RestfulRequest $restfulRequest)
    {
        /** @var IActionHandler|IRepositoryAware $handler */
        $handler = null;

        if ($restfulRequest->resourceId) {
            $handler = $this->make('restful.handlers.document');
        } else {
            $handler = $this->make('restful.handlers.collection');
        }

        if ($handler instanceof IRepositoryAware) {
            $handler->setRepository($this->getRepository());
        }

        return $handler;
    }

    /**
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return \Illuminate\Validation\Validator|null
     */
    protected function validateRequest(RestfulRequest $restfulRequest)
    {
        $rules = $this->getValidationRules();

        if (empty($rules) || in_array($restfulRequest->method, ['GET', 'DELETE', 'OPTIONS', 'HEAD'])) {
            return null;
        }


        $validationRules = [];

        foreach ($rules as $key => $values) {
            $methods = explode('|', $key);
            if (in_array($restfulRequest->method, $methods)) {
                $validationRules = array_merge($validationRules, $values);
            }
        }

        if (empty($validationRules)) {
            return null;
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->getValidationFactory()->make(
            $restfulRequest->input->all(), $validationRules, $this->getCustomValidationMessages()
        );

        return $validator;
    }

    /**
     * @return \App\Restful\IRepository
     */
    public function getRepository()
    {
        if (!$this->repository) {
            if ($this->resourceClass) {
                $this->repository = $this->make('restful.repository', [$this->resourceClass]);
            }
        }

        return $this->repository;
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getCustomValidationMessages()
    {
        return [];
    }

    /**
     * make service
     *
     * @param string $name
     * @param array  $params
     *
     * @return object
     */
    protected function make($name, array $params = [])
    {
        return app()->make($name, $params);
    }

}