<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

interface IRepositoryAware
{
    /**
     * @return IRepository
     */
    public function getRepository();

    /**
     * @param \App\Restful\IRepository $repository
     *
     * @return void
     */
    public function setRepository(IRepository $repository);

}