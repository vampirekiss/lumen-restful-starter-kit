<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Api;

use App\Models\Model;
use App\Restful\Filter;

class Repository implements \App\Restful\Repository
{
    /**
     * model class
     *
     * @var string
     */
    protected $modelClass;

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
     * @param Filter[] $filters
     *
     * @return array
     */
    public function query($filters)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = $this->_call('query');
        foreach ($filters as $filter) {
            list($name, $operator, $value) = $filter->tuple();
            $builder->where($name, $operator, $value);
        }
        return $builder->get()->all();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function queryWithParams(array $params)
    {
        $filters = $this->paramsToFilter($params);
        return $this->query($filters);
    }

    /**
     * @param $params
     *
     * @return array
     */
    protected function paramsToFilter($params)
    {
        $filters = [];
        foreach ($params as $name => $value) {
            $filters[] = new Filter($name, '=', $value);
        }
        return $filters;
    }

    /**
     * @param array $input
     * @param mixed $id
     *
     * @return Model
     */
    public function create($input, $id = null)
    {
        if ($id !== null) {
            $input['id'] = $id;
        }
        /** @var Model $model */
        $model = $this->_call('create', [$input, $id !== null]);
        return $model;
    }

    /**
     * @param mixed $id
     *
     * @return Model
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
     * @param Filter[] $filters
     *
     * @return int
     */
    public function remove($filters)
    {
        return 0;
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function removeWithParams(array $params)
    {
        return 0;
    }


}
