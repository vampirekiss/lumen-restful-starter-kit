<?php

namespace App\Http\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * api handler class
 *
 * @package App\Http\Controllers
 */
abstract class Handler extends Controller
{
    /**
     * handle request
     *
     * @param Request    $request
     * @param mixed|null $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function handleRequest(Request $request, $id = null)
    {
        $method = strtolower($request->getMethod());

        if ($method != 'get' && !$request->isJson()) {
            return $this->respondWithCode(JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $response = $id === null ?
                $this->collection($request, $method) : $this->document($request, $method, $id);
        } catch (\Exception $e) {
            $response = $this->renderException($e);
        }

        if (!$response instanceof JsonResponse) {
            throw new \ErrorException("unexpected return value, must be a JsonResponse instance");
        }

        return $response;
    }

    /**
     * @param \Exception|HttpException $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderException(\Exception $e)
    {
        $codeMaps = [
           ModelNotFoundException::class => JsonResponse::HTTP_NOT_FOUND,
        ];

        $exceptionClass = get_class($e);

        $statusCode = isset($codeMaps[$exceptionClass]) ? $codeMaps[$exceptionClass] : call_user_func(
            function() use ($e) {
                try {
                    $statusCode = $e->getStatusCode();
                } catch (\Exception $_) {
                    $statusCode = $e->getCode();
                }
                return $statusCode;
            }
        );

        if ($statusCode >= 100 && $statusCode <= 599) {
            return $this->respondWithCode($statusCode);
        }

        throw $e;
    }

    protected function document(Request $request, $method, $id)
    {
        if ($method == 'get') {
            $model = $this->getRepository()->findOrFail($id);
            return $this->respond($model);
        }

        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    protected function collection(Request $request, $method)
    {
        if ($method == 'get') {
            return $this->respond($this->getRepository()->all());
        } elseif ($method == 'post') {
            /** @var ParameterBag $json */
            $json = $request->json();
            $this->validateInput($json->all());
            $model = $this->getRepository()->create($json->all());
            return $this->respond($model);
        }
    }

    /**
     * @param mixed $input
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateInput($input)
    {
        $rules = $this->getValidationRules();

        if (empty($rules)) {
            Return;
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->getValidationFactory()->make(
            $input, $rules, $this->getCustomValidationMessages()
        );

        if ($validator->fails()) {
            throw new HttpException(
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                implode("\n", $validator->getMessageBag()->all())
            );
        }
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
     * respond
     *
     * @param        $data
     * @param int    $statusCode
     * @param string $message
     * @param array  $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $statusCode = JsonResponse::HTTP_OK, $message = '', array $headers = [])
    {
        $message = $message ?: JsonResponse::$statusTexts[$statusCode];

        /** @var \Laravel\Lumen\Http\ResponseFactory $factory */
        $factory = response();

        $body = [
            'code'    => $statusCode,
            'data'    => $data,
            'message' => $message
        ];

        return $factory->json($body, $statusCode, $headers);
    }

    /**
     * @param int $statusCode
     *
     * @return JsonResponse
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
