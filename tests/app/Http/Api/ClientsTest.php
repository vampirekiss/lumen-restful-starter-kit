<?php

namespace Tests\App\Http\Api;

class TestClients extends \TestCase
{
    public function testGetClients()
    {
        $this->get('/v1/clients')->assertResponseOk();
    }
}