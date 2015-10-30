<?php

namespace App\Http\Api\User;

use App\Restful\ApiController;


class Users extends ApiController
{
    /**
     * @var string
     */
    protected $resourceClass = \App\Models\User\User::class;


    /**
     * @param \App\Restful\RestfulRequest $request
     *
     * @return array
     */
    protected function getValidationRules($request)
    {
        return [
            'POST|PUT' => [
                'email' => 'required|email',
                'password' => 'required|min:4|max:32'
            ],
            'POST' => [
                'email' => 'unique:users'
            ],
            'PUT' => [
                'email' => 'unique_exclude:users,' . $request->resourceId
            ]
        ];
    }
}