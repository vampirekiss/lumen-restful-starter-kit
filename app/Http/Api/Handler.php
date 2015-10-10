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
    public function handleRequest(Request $request, $id = null)
    {
        $method = strtolower($request->getMethod());

        if ($method != 'get' && !$request->isJson()) {
            return $this->respondWithCode(JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $id === null ?
            $this->collection() : $this->document($id);

        if (!$response instanceof JsonResponse) {
            throw new \ErrorException("unexpected return value, must be a JsonResponse instance");
        }

        return $response;
    }

    protected function document($id)
    {
        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    protected function collection()
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
