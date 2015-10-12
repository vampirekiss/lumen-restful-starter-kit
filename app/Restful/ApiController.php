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
    public static $availableMethods = [
        'GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS', 'HEAD'
    ];

    /**
     * @var array
     */
    public static $writeMethods = [
        'POST', 'PATCH', 'PUT', 'DELETE'
    ];

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
        $formatter = $this->make(IFormatter::class);

        $restfulRequest = $formatter->formatRequest($request);

        $actionHandler = $this->queryActionHandler($restfulRequest);

        if (!$actionHandler) {
            return $this->actionResultBuilder()->setStatusCode(Response::HTTP_NOT_FOUND)->build();
        }

        $validator = $this->validateRequest($restfulRequest);
        if ($validator && $validator->fails()) {
            $actionResult = $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->setMessages($validator->getMessageBag()->all())
                ->build();
        } else {
            $actionResult = $actionHandler->handle($restfulRequest);
        }

        return $formatter->formatActionResult($actionResult);
    }

    /**
     * query action handle
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

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->getValidationFactory()->make(
            $restfulRequest->input->all(), $rules, $this->getCustomValidationMessages()
        );

        return $validator;
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