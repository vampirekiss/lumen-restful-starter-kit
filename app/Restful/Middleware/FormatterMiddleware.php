<?php

/**
 * Created by vampirekiss
 * Copyright (c) 2015 vampirekiss. All rights reserved.
 */

namespace App\Restful\Middleware;

use App\Restful\ActionResultBuilderTrait;
use App\Restful\IFormatter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormatterMiddleware
{
    use ActionResultBuilderTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($request, \Closure $next)
    {
        try {
            return $next($request);
        } catch (NotFoundHttpException $e) {
            /** @var \App\Restful\IFormatter $formatter */
            $formatter = app()->make(IFormatter::class);
            return $formatter->formatActionResult(
                $this->actionResultBuilder()->setStatusCode($e->getStatusCode())
                    ->setMessage($e->getMessage())
                    ->build()
            );
        }
    }

}