<?php

namespace App\Http\Api;

use App\Restful\ApiController;
use \App\Models\Client;

class Clients extends ApiController
{

    /**
     * @var string
     */
    protected $resourceClass = Client::class;

    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return array
     */
    protected function getValidationRules($request)
    {
        return [
            'POST|PUT' => [
                'name' => 'required|max:100',
                'scopes' => 'required',
                'type' => 'required|in:' . implode(',', Client::$allTypes)
            ]
        ];
    }

}