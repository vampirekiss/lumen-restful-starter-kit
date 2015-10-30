<?php

namespace Tests\App\Http\Api;

class TestUsers extends \TestCase
{
    public function testGetUsers()
    {
        $this->get('/v1/users')->assertResponseOk();
    }
}