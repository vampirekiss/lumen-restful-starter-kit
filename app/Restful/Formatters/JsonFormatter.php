<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Formatters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Restful\ActionResult;
use App\Restful\IFormatter;
use App\Restful\RestfulRequest;


class JsonFormatter implements IFormatter
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Restful\RestfulRequest
     */
    public function formatRequest(Request $request)
    {
        $restfulRequest = new RestfulRequest();

        if (in_array($request->getMethod(), ['POST', 'PATCH', 'PUT'])) {
            $input = $request->json();
        } else {
            $input = $request->query;
        }

        if ($input->has('per-page')) {
            $restfulRequest->perPage = intval($input->get('per-page'));
            $input->remove('per-page');
        }

        if ($input->has('page')) {
            $restfulRequest->page = intval($input->get('page'));
            $input->remove('page');
        }

        $restfulRequest->method = $request->getMethod();
        $restfulRequest->input = $input;

        $params = $request->route()[2];
        if (isset($params['id'])) {
            $restfulRequest->resourceId = $params['id'];
        }

        return $restfulRequest;
    }

    /**
     * @param \App\Restful\ActionResult $result
     *
     * @return \Illuminate\Http\Response
     */
    public function formatActionResult(ActionResult $result)
    {
        $message = $result->message ? $result->message : Response::$statusTexts[$result->statusCode];

        $data = $this->_morphToArray($result->data);

        $json = [
            'code'    => $result->statusCode,
            'data'    => $data,
            'message' => $message,
        ];

        /** @var \Laravel\Lumen\Http\ResponseFactory $factory */
        $factory = response();
        return $factory->make($json, $result->statusCode, $result->headers);
    }

    /**
     * @param mixed $data data
     *
     * @return array
     */
    private function _morphToArray($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        } elseif ($data instanceof \JsonSerializable) {
            return $data->jsonSerialize();
        }
        return $data;
    }
}