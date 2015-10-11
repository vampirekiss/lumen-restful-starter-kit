<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

class Filter
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @param string $name
     * @param string $operator
     * @param mixed $value
     */
    public function __construct($name, $operator, $value)
    {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function tuple()
    {
        return [$this->name, $this->operator, $this->value];
    }
}