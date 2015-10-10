<?php

namespace App\Http\Api\User;

use App\Http\Api\Handler;
use App\Models\User\UserRepository;

class User extends Handler
{
    /**
     * @var \App\Models\User\UserRepository
     */
    protected $repository;

    /**
     * User constructor.
     *
     * @param \App\Models\User\UserRepository $repository
     *
     * @return User
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return \App\Models\Repository
     */
    protected function getRepository()
    {
        return $this->repository;
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