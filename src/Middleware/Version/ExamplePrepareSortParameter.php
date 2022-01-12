<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Illuminate\Http\Request;

class ExamplePrepareSortParameter
{
    /**
     * @param           $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set the default parameter because it is required in a newer version.
        $request->query->set('sort', 'DESC');

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        return $response;
    }
}
