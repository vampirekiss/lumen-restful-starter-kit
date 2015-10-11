<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

interface Repository
{

    /**
     * @param Filter[] $filters
     *
     * @return array
     */
    public function query($filters);

    /**
     * @param array $params
     *
     * @return array
     */
    public function queryWithParams(array $params);

    /**
     * @param array $input
     * @param mixed $id
     *
     * @return mixed
     */
    public function create($input, $id = null);

    /**
     * @param mixed $id
     *
     * @return mixed
     */
    public function retrieve($id);

    /**
     * @param mixed $id
     * @param array $input
     *
     * @return mixed
     */
    public function update($id, $input);

    /**
     * @param Filter[] $filters
     *
     * @return int
     */
    public function remove($filters);

    /**
     * @param array $params
     *
     * @return int
     */
    public function removeWithParams(array $params);

}