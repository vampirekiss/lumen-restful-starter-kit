<?php

namespace App\Console\Commands;


trait DotNameSpaceParser
{
    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $name = parent::parseName($name);
        return str_replace('.', '\\', $name);
    }

}