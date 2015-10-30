<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Validation\Validator as BaseValidator;

class Validator extends BaseValidator
{
    /**
     * extend rule "unique_exclude"
     *
     * @param string $attribute
     * @param string $value
     * @param array  $params
     *
     * @return bool
     */
    protected function validateUniqueExclude($attribute, $value, $params)
    {
        $this->requireParameterCount(2, $params, 'unique_exclude');

        list($connection, $table) = $this->parseTable($params[0]);

        $excludeId = intval($params[1]);

        /** @var \Illuminate\Database\ConnectionResolverInterface  $db $db */
        $db = app()->make('db');

        $result = $db->connection($connection)->table($table)->where($attribute, '=', $value)->first(['id']);
        if ($result === null) {
            return true;
        }

        return $result->id == $excludeId;
    }

    /**
     * extend rule "readonly"
     *
     * @return bool
     */
    protected function validateReadonly()
    {
        return false;
    }
}