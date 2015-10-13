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
     * paginate resources by params
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     * @param int      $page
     * @param int      $perPage
     *
     * @return mixed
     */
    public function paginateByParams(ParameterBag $params, $page = null, $perPage = null);


    /**
     * create new resource
     *
     * @param array $input
     * @param int $id
     *
     * @return mixed
     */
    public function create($input, $id = null);

    /**
     * retrieve a resource by id
     *
     * @param int $id
     *
     * @return mixed
     */
    public function retrieve($id);

    /**
     * update a resource by id
     *
     * @param int $id
     * @param array $input
     *
     * @return mixed
     */
    public function update($id, $input);

    /**
     * replace a resource by id
     *
     * @param int $id
     * @param array $input
     *
     * @return mixed
     */
    public function replace($id, $input);

    /**
     * remove a resource by id
     *
     * @param int $id
     *
     * @return bool
     */
    public function remove($id);

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