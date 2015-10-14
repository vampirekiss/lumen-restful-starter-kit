<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful;

use Illuminate\Support\Str;
use Laravel\Lumen\Application;
use App\Restful\Middleware;

class RouteRuleBuilder
{
    /**
     * @var array
     */
    public static $accessableMethods = [
        'GET', 'HEAD', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'
    ];

    /**
     * @var string
     */
    private $_prefix = '';

    /**
     * @var string
     */
    private $_version = '';

    /**
     * @var string
     */
    private $_baseNamespace = '';

    /**
     * @var array
     */
    private $_rules = [];

    /**
     * @var array
     */
    public $middleware;

    /**
     * construct
     *
     * @param array $middleware
     *
     * @return RouteRuleBuilder
     */
    public function __construct(array $middleware = [])
    {
        $this->middleware = $middleware;
    }

    /**
     * @param string $name
     * @param string $middleware
     *
     * @return $this
     */
    public function addMiddleware($name, $middleware)
    {
        $this->middleware[$name] = $middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;

        return $this;
    }

    /**
     * @param $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->_version = $version;

        return $this;
    }

    /**
     * @param string $baseNamespace
     *
     * @return $this
     */
    public function setBaseNamespace($baseNamespace)
    {
        $this->_baseNamespace = $baseNamespace;

        return $this;
    }

    /**
     * @return $this
     */
    public function withCors()
    {
        $this->addMiddleware('cors', Middleware\CorsMiddleware::class);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function withAuth($path = '/auth')
    {
        $prefix = $this->_prefix . '/' . $this->_version;
        $authPath = $prefix != '/' ? sprintf('%s%s', $prefix, $path) : $path;
        $this->_rules[$authPath] = $path;
        $this->addMiddleware('auth', Middleware\AuthMiddleware::class . ':' . $authPath);

        return $this;
    }

    /**
     * @param $class
     * @param $uri
     *
     * @return $this
     */
    public function mapping($class, $uri)
    {
        $this->_rules[$class] = $uri;

        return $this;
    }

    /**
     * @param array $rules ['class' => 'uri']
     *
     * @return $this
     */
    public function mappingFromRules(array $rules)
    {
        foreach ($rules as $class => $uri) {
            $this->mapping($class, $uri);
        }

        return $this;
    }

    /**
     * @param \Laravel\Lumen\Application $app
     */
    public function buildToApp(Application $app)
    {
        $prefix = $this->_prefix . '/' . $this->_version;

        if ($prefix != '/') {
            $groupOptions = ['prefix' => $prefix, 'middleware' => $this->getMiddleware()];
            $app->group($groupOptions, function () use ($app) {
                $this->_buildRouteRules($app);
            });
        } else {
            $this->_buildRouteRules($app);
        }
    }

    /**
     * @param \Laravel\Lumen\Application $app
     */
    private function _buildRouteRules(Application $app)
    {
        foreach ($this->_rules as $class => $regexpUri) {
            $class = $this->_baseNamespace ? sprintf('%s.%s', $this->_baseNamespace, $class) : $class;
            $class = str_replace('.', '\\', $class);
            $action = sprintf('%s@handle', $class);
            $uris = $this->_parseUri($regexpUri);
            foreach (static::$accessableMethods as $method) {
                foreach ($uris as $uri) {
                    $app->addRoute($method, $uri, $action);
                }
            }
        }
    }

    /**
     * @param string $uri
     *
     * @return array
     */
    private function _parseUri($uri)
    {
        $uris = [];
        if (Str::contains($uri, '{id?}')) {
            $uris[] = str_replace('{id?}', '', $uri);
            $uris[] = str_replace('?', '', $uri);
        } else {
            $uris[] = $uri;
        }

        return $uris;
    }
}