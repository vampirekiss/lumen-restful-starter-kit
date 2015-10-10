<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query()
    {
        return $this->_call('query');
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->_call('all');
    }

    /**
     * @param array $props
     *
     * @return \Illuminate\Support\Collection
     */
    public function findByProps(array $props)
    {
        $query = $this->query();

        foreach ($props as $prop => $value) {
            $query->where($prop, '=', $value);
        }

        return $query->get()->all();
    }


    /**
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($id)
    {
        return $this->query()->findOrFail($id)->first();
    }

    /**
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        return $this->_call('destroy', [$id]);
    }

    /**
     * @param array $attributes
     * @param bool  $exists
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($attributes = [], $exists = false)
    {
        /** @var Model $model */
        $model = $this->_call('create', [$attributes, $exists]);
        return $model;
    }

}