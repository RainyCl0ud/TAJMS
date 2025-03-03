<?php

namespace App\Http\Middleware;


use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


class Kernel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $routeMiddleware = [
    
        'role' => RoleMiddleware::class,
    ];
    protected $middlewareGroups = [
        'web' => [
            // Other middleware
            VerifyCsrfToken::class,
        ],
    ];
}
