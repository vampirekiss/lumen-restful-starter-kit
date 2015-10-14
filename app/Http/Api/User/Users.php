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
                'cellphone' => 'required|min:11|max:11',
                'password' => 'required|min:4|max:32'
            ],
            'POST' => [
                'cellphone' => 'unique:users'
            ],
            'PUT' => [
                'cellphone' => 'unique_exclude:users,' . $request->resourceId
            ],
            'PATCH' => [
                'level_id' => 'required'
            ]
        ];
    }
}