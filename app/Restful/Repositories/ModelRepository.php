<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Repositories;

use App\Restful\IRepository;
use Illuminate\Support\Str;
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
     * @param \Symfony\Component\HttpFoundation\ParameterBag $params
     * @param int                                            $page
     * @param int                                            $perPage
     *
     * @return array
     */
    public function paginateByParams(ParameterBag $params, $page = null, $perPage = null)
    {
        $query = $this->_query();

        if ($this->queryDelegate) {
            $query = call_user_func_array($this->queryDelegate, [$query, $params]);
        } else {
            foreach ($params as $name => $value) {
                if ($this->hasColumn($name)) {
                    $query->where($name, '=', $value);
                }
            }
        }

        !$perPage && $perPage = intval(env('RESTFUL_PER_PAGE', 40));
        !$page && $page = 1;

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $array = $paginator->toArray();

        return [
            'total'         => $array['total'],
            'per_page'      => $array['per_page'],
            'next_page_ur'  => $array['next_page_url'] ?: '',
            'prev_page_url' => $array['prev_page_url'] ?: '',
            'current_page'  => $array['current_page'],
            'last_page'     => max($array['last_page'], 1),
            'list'          => $array['data']
        ];
    }

    /**
     * remove a resource by id
     *
     * @param int $id
     *
     * @return bool
     */
    public function remove($id)
    {
        if ($id <= 0) {
            return false;
        }

        return $this->_query()->getQuery()->delete($id) > 0;
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

        $query = $this->_query();

        $hasSetCondition = false;

        if ($this->queryDelegate) {
            $query = call_user_func_array($this->queryDelegate, [$query, $params]);
            $hasSetCondition = true;
        } else {
            foreach ($params as $name => $value) {
                if (!empty($name) && $this->hasColumn($name)) {
                    $query->where($name, '=', $value);
                    $hasSetCondition = true;
                }
            }
        }

        return $hasSetCondition ? $query->delete() : 0;
    }

    /**
     * @param array $input
     * @param int   $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($input, $id = null)
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        if ($id === null) {
            $model = $this->_call('create', [$input]);
        } else {
            $model = new $this->modelClass($input);
            $model->setAttribute('id', $id);
            $model->save();
        }

        return $model->fresh();
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function retrieve($id)
    {
        if ($id <= 0) {
            return null;
        }
        return $this->_call('find', [$id]);
    }

    /**
     * @param int $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model|null
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
     * replace a resource by id
     *
     * @param int $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function replace($id, $input)
    {
        $model = $this->retrieve($id);

        if (!$model) {
            if ($id > 0) {
                return $this->create($input, $id);
            }
            return null;
        }

        return $this->update($id, $input);
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function _query()
    {
        return $this->_call('query');
    }

    /**
     * get model table columns
     *
     * @return mixed
     */
    protected function getColumns()
    {
        static $columns = [];

        if (!isset($columns[$this->modelClass])) {
            /** @var \Illuminate\Database\Connection $connection */
            $connection = $this->_query()->getQuery()->getConnection();
            $columns[$this->modelClass] = $connection->getSchemaBuilder()->getColumnListing(
                str_replace('\\', '', Str::snake(Str::plural(class_basename($this->modelClass))))
            );
        }

        return $columns[$this->modelClass];
    }

    /**
     * @param string $column
     *
     * @return bool
     */
    protected function hasColumn($column)
    {
        foreach ($this->getColumns() as $col) {
            if (strtolower($column) == strtolower($col)) {
                return true;
            }
        }

        return false;
    }


}
