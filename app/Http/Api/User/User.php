<?php

namespace App\Http\Api\User;

use App\Restful\ApiController;

class User extends ApiController
{
    /**
     * User constructor.
     *
     * @param \App\Http\Api\User\UserRepository $repository
     *
     * @return User
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

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