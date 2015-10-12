<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

trait ActionResultBuilderTrait
{
    /**
     * @return \App\Restful\ActionResultBuilder
     */
    protected function actionResultBuilder()
    {
        return new ActionResultBuilder();
    }

}