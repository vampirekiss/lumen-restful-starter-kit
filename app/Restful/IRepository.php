<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * resource repository interface
 */
interface IRepository
{

    /**
     * finds resources by params
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     * @param int      $page
     * @param int      $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByParams(ParameterBag $params, $page = null, $perPage = null);


    /**
     * create new resource
     *
     * @param array $input
     * @param mixed $id
     *
     * @return mixed
     */
    public function create($input, $id = null);

    /**
     * retrieve a resource by id
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id);

    /**
     * update a resource by id
     *
     * @param mixed $id
     * @param array $input
     *
     * @return mixed
     */
    public function update($id, $input);

    /**
     * remove resources by params
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     *
     * @return int
     */
    public function removeByParams(ParameterBag $params);

    /**
     * set query delegate
     *
     * @param callable $delegate
     *
     * @return void
     */
    public function setQueryDelegate($delegate);

}