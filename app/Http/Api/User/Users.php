<?php

namespace App\Http\Api\User;

use App\Restful\ApiController;
use App\Models\User\User;


class Users extends ApiController
{
    /**
     * @var string
     */
    protected $resourceClass = User::class;

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        // todo: implements this patterns
        return [
            'POST|PUT' => [
                'cellphone' => 'required|min:11|max:11',
                'password' => 'required|min:4|max:32'
            ],
            'POST' => [
                'cellphone' => 'unique:users'
            ],
            'PUT' => [
                'cellphone' => 'other-unique:users' // replace, do not check self
            ],
            'PATCH' => [
                'level_id' => 'required'
            ]
        ];
    }


}