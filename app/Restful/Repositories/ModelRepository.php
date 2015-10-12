<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Repositories;

use App\Restful\IRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

class ModelRepository implements IRepository
{
    /**
     * model class
     *
     * @var string
     */
    protected $modelClass;

    /**
     * @var callable
     */
    protected $queryDelegate;

    /**
     * @param $modelClass
     *
     * @return ModelRepository
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    private function _call($method, $params = [])
    {
        $callable = [$this->modelClass, $method];
        return call_user_func_array($callable, $params);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     * @param int                                            $page
     * @param int                                            $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function findByParams(ParameterBag $params, $page = null, $perPage = null)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $this->_call('query');

        if ($this->queryDelegate) {
            return call_user_func_array($this->queryDelegate, [$query, $params]);
        }

        foreach ($params as $name => $value) {
            $query->where($name, '=', $value);
        }

        !$perPage && $perPage = intval(env('RESTFUL_PER_PAGE', 40));
        !$page && $page = 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     *
     * @return int
     */
    public function removeByParams(ParameterBag $params)
    {
        if (empty($params)) {
            return 0;
        }

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $this->_call('query');

        if ($this->queryDelegate) {
            return call_user_func_array($this->queryDelegate, [$query, $params]);
        }

        $hasSetCondition = false;
        foreach ($params as $name => $value) {
            if (!empty($name)) {
                $query->where($name, '=', $value);
                $hasSetCondition = true;
            }
        }

        return $hasSetCondition ? $query->delete() : 0;
    }


    /**
     * @param array $input
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($input, $id = null)
    {
        if ($id !== null) {
            $input['id'] = $id;
        }
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->_call('create', [$input, $id !== null]);
        return $model;
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function retrieve($id)
    {
        return $this->_call('find', [$id]);
    }

    /**
     * @param mixed $id
     * @param array $input
     *
     * @return mixed
     */
    public function update($id, $input)
    {
        $model = $this->retrieve($id);
        if (!$model) {
            return null;
        }

        foreach ($input as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->save();

        return $model;
    }

    /**
     * @param callable $delegate
     *
     * @return void
     */
    public function setQueryDelegate($delegate)
    {
        $this->queryDelegate = $delegate;
    }

}
