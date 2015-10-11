<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Http\Api\User;

use App\Http\Api\Repository;
use App\Models\User\User;

class UserRepository extends Repository
{
    protected $modelClass = User::class;
}
