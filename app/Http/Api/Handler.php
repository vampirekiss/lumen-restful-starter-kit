<?php

namespace App\Http\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Restful\Repository;
use App\Restful\Representation;
use App\Restful\ResourceActionFactory;
use App\Restful\Request as RestfulRequest;

/**
 * api handler class
 *
 * @package App\Http\Controllers
 */
abstract class Handler extends Controller
{
    public static $availableMethods = [
        'GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS', 'HEAD'
    ];

    /**
     * @var JsonResponse
     */
    private $_response;

    /**
     * @var Representation
     */
    private $_representation;

    /**
     * handle request
     *
     * @param Request    $request
     * @param mixed|null $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function handle(Request $request, $id = null)
    {
        $method = $request->getMethod();

        if ($method != 'GET' && !$request->isJson()) {
            return $this->respondWithCode(
                JsonResponse::HTTP_BAD_REQUEST, 'missing header "Content-Type: application/json"'
            );
        }

        $isWriteOperation = in_array($method, ['POST', 'PATCH', 'PATCH', 'DELETE']);

        $hasException = false;

        /** @var \Illuminate\Database\Connection  $db */
        $db = app()->make('db');

        if ($isWriteOperation) {
            $db->beginTransaction();
        }

        try {
            $id === null ?
                $this->collection($request) : $this->document($request, $id);
            $response = $this->respond($this->getRepresentation()->getContent());
        } catch (\Exception $e) {
            $response = $this->renderException($e);
            $hasException = true;
        }

        if ($isWriteOperation) {
            $hasException ?  $db->rollBack() : $db->commit();
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
            ValidationException::class    => JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ];

        $exceptionClass = get_class($e);

        $statusCode = isset($codeMaps[$exceptionClass]) ? $codeMaps[$exceptionClass] : call_user_func(
            function () use ($e) {
                return method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
            }
        );

        if ($statusCode >= 100 && $statusCode <= 599) {
            return $this->respond(
                null, $statusCode, $this->buildExceptionMessage($e, $statusCode)
            );
        }

        throw $e;
    }

    /**
     * @param \Exception $e
     * @param  int       $statusCode
     *
     * @return string
     */
    protected function buildExceptionMessage(\Exception $e, $statusCode)
    {
        if ($e instanceof ModelNotFoundException) {
            return JsonResponse::$statusTexts[$statusCode];
        }

        if ($e instanceof ValidationException) {
            return implode("\n", $e->errors()->all());
        }

        return $e->getMessage();
    }

    protected function document(Request $request, $id)
    {
        return $this->respondWithCode(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function collection(Request $request)
    {
        $collection = ResourceActionFactory::createCollection(
            $this->getRepository(), $this->getRepresentation()
        );

        $collection->handle($this->_prepareRequest($request));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $id
     *
     * @return \App\Restful\Request
     */
    private function _prepareRequest(Request $request, $id = null)
    {
        $restfulRequest = new RestfulRequest();

        if (in_array($request->getMethod(), ['POST', 'PATCH', 'PUT'])) {
            $input = $request->json();
            $this->validateInput($input);
        } else {
            $input = $request->query;
        }

        $restfulRequest->method = $request->getMethod();
        $restfulRequest->input = $input;
        $restfulRequest->resourceId = $id;

        return $restfulRequest;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = response()->json();
        }
        return $this->_response;
    }

    /**
     * @param JsonResponse $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @return \App\Restful\Representation
     */
    public function getRepresentation()
    {
        if (!$this->_representation) {
            $this->_representation = new Representation($this->getResponse());
        }
        return $this->_representation;
    }

    /**
     * @param Representation $representation
     */
    public function setRepresentation(Representation $representation)
    {
        $this->_representation = $representation;
    }


    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $input
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
            $input->all(), $rules, $this->getCustomValidationMessages()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
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
    protected function respond($data, $statusCode = null, $message = '', array $headers = [])
    {
        /** @var JsonResponse $response */
        $response = $this->getResponse();;

        if ($statusCode !== null) {
            $response->setStatusCode($statusCode);
        }

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        $message = $message ?: JsonResponse::$statusTexts[$response->getStatusCode()];

        $body = [
            'code'    => $statusCode,
            'data'    => $data,
            'message' => $message
        ];

        $response->setData($body);

        return $response;
    }

    /**
     * @param int    $statusCode
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCode($statusCode, $message = '')
    {
        return $this->respond(null, $statusCode, $message);
    }


    /**
     * @return Repository
     */
    protected abstract function getRepository();

}
