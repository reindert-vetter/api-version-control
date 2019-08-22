<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Tests\Unit\MiddlewareVersion\Mock;

use Closure;
use Illuminate\Http\Request;

class MockVersionMiddleware
{
    /**
     * @param           $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response;
    }
}