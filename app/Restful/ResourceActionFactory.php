<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

class ResourceActionFactory
{

    public static function createDocument(Repository $repository, Representation $representation)
    {
        return new Document($repository, $representation);
    }

    public static function createCollection(Repository $repository, Representation $representation)
    {
        return new Collection($repository, $representation);
    }

}
