<?php

use Illuminate\Support\Str;

include 'rebind.php';

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        rebind($this->app);
    }

    /**
     * Turn the given URI into a fully qualified URL.
     *
     * @param  string $uri
     *
     * @return string
     */
    protected function prepareUrlForRequest($uri)
    {
        $uri = parent::prepareUrlForRequest($uri);

        if (Str::contains('?', $uri)) {
            $uri .= '&token=' . DummyAuthBundle::$token;
        } else {
            $uri .= '?token=' . DummyAuthBundle::$token;
        }

        return $uri;
    }


}
