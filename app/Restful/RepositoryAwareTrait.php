<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

trait RepositoryAwareTrait
{

    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * @return IRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param \App\Restful\IRepository $repository
     *
     * @return void
     */
    public function setRepository(IRepository $repository)
    {
        $this->repository = $repository;
    }

}