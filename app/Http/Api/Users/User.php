<?php

namespace App\Http\Api\Users;

use App\Http\Api\Handler;
use App\Models\Users\UserRepository;


class User extends Handler
{
    /**
     * api uri
     *
     * @var string
     */
    public static $uri = '/users';

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @param UserRepository $repository
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

}