<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Security;

use App\Restful\ActionResultBuilderTrait;
use App\Restful\Exceptions\RestfulException;
use App\Restful\IFormatter;
use Illuminate\Http\Response;


class AuthHandler
{
    use ActionResultBuilderTrait;

    /**
     * @var \App\Restful\Security\IAuthBundle
     */
    protected $bundle;

    /**
     * @param \App\Restful\Security\IAuthBundle $bundle
     */
    public function __construct(IAuthBundle $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $authPath
     *
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $authPath)
    {
        /** @var \App\Restful\IFormatter $formatter */
        $formatter = app()->make(IFormatter::class);
        $restfulRequest = $formatter->formatRequest($request);

        try {
            if (strtolower($authPath) == strtolower($request->path())) {
                return $formatter->formatActionResult($this->_authenticate($restfulRequest));
            }
            $result = $this->_authorize($restfulRequest);
            if ($result) {
                return $formatter->formatActionResult($result);
            }
        } catch (RestfulException $e) {
            return $formatter->formatActionResult($e->toActionResult());
        }

        return null;
    }

    /**
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return \App\Restful\ActionResult
     */
    private function _authenticate($restfulRequest)
    {
        if (!in_array($restfulRequest->method, ['GET', 'POST'])) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
                ->build();
        }

        $credential = $this->bundle->authenticate($restfulRequest);
        if (!$credential) {
            $actionResult = $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED)
                ->build();
        } else {
            $actionResult = $this->actionResultBuilder()
                ->setResource([
                    'token' => $credential->getToken(),
                    'expires_in'   => $credential->getTokenExpiresIn()
                ])->build();
        }

        return $actionResult;
    }

    /**
     * @param \App\Restful\RestfulRequest $restfulRequest
     *
     * @return \App\Restful\ActionResult|null
     */
    private function _authorize($restfulRequest)
    {
        if (!$this->bundle->isAuthorized($restfulRequest)) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_UNAUTHORIZED)
                ->build();
        }

        if (!$this->bundle->hasRights($restfulRequest)) {
            return $this->actionResultBuilder()
                ->setStatusCode(Response::HTTP_FORBIDDEN)
                ->build();
        }

        return null;
    }

}