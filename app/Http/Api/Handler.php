<?php

namespace App\Http\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller;

/**
 * api handler class
 *
 * @package App\Http\Controllers
 */
abstract class Handler extends Controller
{
    /**
     * api uri
     *
     * @var string
     */
    public static $uri = '';

    /**
     * @var string
     */
    protected $lastMessage = '';

    /**
     * unify body
     *
     * @param $data
     * @param $statusCode
     * @param $message
     *
     * @return array
     */
    public final static function unifyBody($data, $statusCode, $message = '')
    {
        return [
            'code'    => $statusCode,
            'data'    => $data,
            'message' => $message
        ];
    }

    /**
     * handle request
     *
     * @param Request $request
     *
     * @throws \ErrorException
     * @return JsonResponse
     */
    public function handleRequest(Request $request)
    {
        $method = strtolower($request->getMethod());

        if ($method != 'get' && !$request->isJson()) {
            return $this->respondWithCode(JsonResponse::HTTP_BAD_REQUEST);
        }

        $callable = [$this, $method];

        if (!is_callable($callable)) {
            return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
        }

        $response = call_user_func_array($callable, [$request]);
        
        if (!$response instanceof JsonResponse) {
            throw new \ErrorException(
                sprintf("%s::%s must return a JsonResponse instance", get_called_class(), $method)
            );
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function get(Request $request)
    {
        $response = $this->respond(
            $this->getRepository()->findByProps($request->query->all())
        );
        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function post(Request $request)
    {
        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function put(Request $request)
    {
        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function delete(Request $request)
    {
        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }


    /**
     * set message for JsonResponse body
     *
     * @param string $message
     *
     * @return $this
     */
    protected function withMessage($message)
    {
        $this->lastMessage = $message;
    }

    /**
     * respond
     *
     * @param       $data
     * @param int   $statusCode
     * @param array $headers
     * @param int   $options
     *
     * @return JsonJsonResponse
     */
    protected function respond($data, $statusCode = JsonResponse::HTTP_OK, array $headers = [], $options = 0)
    {
        /** @var \Laravel\Lumen\Http\ResponseFactory $factory */
        $factory = response();

        $message = $this->lastMessage ? $this->lastMessage : JsonResponse::$statusTexts[$statusCode];

        $body = $this->unifyBody($data, $statusCode, $message);

        return $factory->json($body, $statusCode, $headers, $options);
    }


    /**
     * @param int $statusCode
     *
     * @return JsonJsonResponse
     */
    protected function respondWithCode($statusCode)
    {
        return $this->respond(null, $statusCode);
    }


    /**
     * @return \App\Models\Repository
     */
    protected abstract function getRepository();
}
