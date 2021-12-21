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
     * @var array|null
     */
    private $config;

    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $pipes = MiddlewareCollection::createFromConfig($request, $this->config)
            ->filterByVersionCompare()
            ->permitVersionStatement()
            ->flatten()
            ->rejectNonPipe()
            ->unique()
            ->reverse()
            ->toArray();

        $request->route()->forgetParameter('version');
        $response = (new Pipeline(Container::getInstance()))
            ->send($request)
            ->through($pipes)
            ->then($next);

        return $response;
    }
}
