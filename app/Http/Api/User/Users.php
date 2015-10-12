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
        return [
            'cellphone' => 'required|unique:users|max:11',
            'password' => 'required|min:4|max:32'
        ];

    }


}