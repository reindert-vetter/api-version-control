<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiVersionControl
{
    /**
     * @param           $request
     * @param \Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $pipes = MiddlewareCollection::createFromConfig($request)
                                     ->permitVersionStatement()
                                     ->filterByVersionCompare()
                                     ->flatten()
                                     ->rejectNonPipe()
                                     ->unique()
                                     ->reverse()
                                     ->toArray();

        $response = (new Pipeline(Container::getInstance()))
            ->send($request)
            ->through($pipes)
            ->then($next);

        return $response;
    }
}