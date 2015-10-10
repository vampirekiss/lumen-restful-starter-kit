<?php

namespace App\Http\Api\User;

use App\Http\Api\Handler;
use App\Models\User\TokenRepository;

class Token extends Handler
{
    /**
     * @var \App\Models\User\TokenRepository
     */
    protected $repository;

    /**
     * Token constructor.
     *
     * @param \App\Models\User\TokenRepository $repository
     *
     * @return Token
     */
    public function __construct(TokenRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return \App\Models\User\TokenRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}