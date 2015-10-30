<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Laravel\Lumen\Routing\Controller;
use App\Restful\Exceptions\RestfulException;
use App\Restful\ActionHandlers\DocumentHandler;
use App\Restful\ActionHandlers\CollectionHandler;


/**
 * Restful api controller
 */
abstract class ApiController extends Controller implements IHttpHandler, IRepositoryAware
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
     * @return array
     */
    public static function allowMethods()
    {
        return ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE'];
    }

    /**
     * handle request
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        /** @var \App\Restful\IFormatter $formatter */
        $formatter = $this->make(IFormatter::class);

        if (!in_array($request->getMethod(), static::allowMethods())) {
            $actionResult = $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
                ->build();

            return $formatter->formatActionResult($actionResult);
        }

        try {
            $actionResult = $this->dispatchRequest($formatter, $request);
        } catch (\Exception $e) {
            if ($e instanceof RestfulException) {
                $actionResult = $e->toActionResult();
            } else {
                if (app()->environment() == 'production') {
                    app()->make(ExceptionHandler::class)->report($e);

                    return $formatter->formatActionResult(
                        $this->actionResultBuilder()
                            ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                            ->build()
                    );
                }
                throw $e;
            }
        }

        return $formatter->formatActionResult($actionResult);
    }

    /**
     * dispatch restful request to action handler
     *
     * @param \App\Restful\IFormatter  $formatter
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function dispatchRequest($formatter, $request)
    {
        $restfulRequest = $formatter->formatRequest($request);
        $actionHandler = $this->queryActionHandler($restfulRequest);

        if (!$actionHandler) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_NOT_FOUND)
                ->build();
        }

        $actionResult = null;

        if ($actionHandler instanceof IActionHandler && $actionHandler->shouldValidateRequest($restfulRequest)) {
            $validator = $this->validateRequest($restfulRequest);
            if ($validator && $validator->fails()) {
                $actionResult = $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessages($validator->getMessageBag()->all())
                    ->build();
            }
        }

        if ($actionResult === null) {
            $handle = function () use ($actionHandler, $restfulRequest) {
                if (is_callable($actionHandler)) {
                    return $actionHandler($restfulRequest);
                }

                return $actionHandler->handle($restfulRequest);
            };
            if ($this->autoStartTransaction && in_array($restfulRequest->method, self::$writeMethods)) {
                /** @var \Illuminate\Database\Connection $db */
                $db = $this->make('db');
                $actionResult = $db->transaction(function () use ($handle, $restfulRequest) {
                    return $handle($restfulRequest);
                });
            } else {
                $actionResult = $handle($restfulRequest);
            }
        }

        return $actionResult;
    }

    /**
     * query action handler
     *
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return \App\Restful\IActionHandler|callable
     */
    protected function queryActionHandler(RestfulRequest $restfulRequest)
    {
        /** @var IActionHandler|IRepositoryAware $handler */
        $handler = null;

        if ($this->resourceClass != null) {
            if ($restfulRequest->resourceId > 0) {
                $handler = $this->make(DocumentHandler::class);
            } elseif ($restfulRequest->resourceId === 0) {
                $handler = $this->make(CollectionHandler::class);
            } else {
                return function () {
                    return $this->actionResultBuilder()->setStatusCode(Response::HTTP_NOT_FOUND)
                        ->build();
                };
            }

            if ($handler instanceof IRepositoryAware) {
                $handler->setRepository($this->getRepository());
            }
        } else {
            $handler = [$this, 'doAction'];
        }

        return $handler;
    }

    /**
     * @param string                      $method
     * @param \App\Restful\RestfulRequest $restfulRequest
     * @param array                       $rules
     * @param callable                    $callback
     *
     * @return \App\Restful\ActionResult
     */
    protected function actionValidatePassOrFail($method, RestfulRequest $restfulRequest, array $rules, $callback)
    {
        if ($method != $restfulRequest->method) {
            return $this->actionResultBuilder()->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
                ->build();
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->getValidationFactory()->make(
            $restfulRequest->input->all(), $rules
        );

        if ($validator->fails()) {
            return $this->actionResultBuilder()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->setMessages($validator->getMessageBag()->all())
                ->build();
        }

        return $callback($restfulRequest);
    }

    /**
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return \Illuminate\Validation\Validator|null
     */
    protected function validateRequest(RestfulRequest $restfulRequest)
    {
        $rules = $this->getValidationRules($restfulRequest);

        if (empty($rules) || in_array($restfulRequest->method, ['GET', 'DELETE', 'OPTIONS', 'HEAD'])) {
            return null;
        }

        $validationRules = [];

        foreach ($rules as $key => $values) {
            $methods = explode('|', $key);
            if (in_array($restfulRequest->method, $methods)) {
                $validationRules = $this->_mergeValidationRules($validationRules, $rules[$key]);
            }
        }

        if (empty($validationRules)) {
            return null;
        }

        return $this->getValidationFactory()->make(
            $restfulRequest->input->all(), $validationRules
        );
    }

    /**
     * @param array $originRules
     * @param array $mergeRules
     *
     * @return array
     */
    private function _mergeValidationRules(array $originRules, array $mergeRules)
    {
        foreach ($originRules as $key => $value) {
            if (isset($mergeRules[$key])) {
                $originRules[$key] = sprintf('%s|%s', $value, $mergeRules[$key]);
                unset($mergeRules[$key]);
            }
        }

        return array_merge($originRules, $mergeRules);
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return \App\Restful\ActionResult
     */
    protected function doAction(RestfulRequest $request)
    {
        return $this->actionResultBuilder()->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
            ->build();
    }

    /**
     * @return \App\Restful\IRepository
     */
    public function getRepository()
    {
        if (!$this->repository) {
            if ($this->resourceClass) {
                $this->repository = $this->make(IRepository::class, [$this->resourceClass]);
            }
        }

        return $this->repository;
    }

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return array
     */
    protected function getValidationRules($request)
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