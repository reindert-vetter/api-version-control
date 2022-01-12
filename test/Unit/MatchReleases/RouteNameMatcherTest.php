<?php
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\MatchReleases;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection;
use ReindertVetter\ApiVersionControl\Helper\RouteNameMatcher;
use ReindertVetter\ApiVersionControl\Middleware\Version\ExamplePrepareParameterException;

class RouteNameMatcherTest extends TestCase
{
    public function test(): void
    {
        $config = [
            'releases'      => [
                'orders.index' => [
                    '<=2' => [
                        ExamplePrepareParameterException::class,
                    ],
                ],
                'default'      => [],
            ],
            'route_matcher' => RouteNameMatcher::class,
        ];

        $request = new Request();
        $request->server->set('REQUEST_URI', '/v2/orders');
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', 'the_route', []))->bind($request)->name('orders.index');
        });

        $collection = MiddlewareCollection::createFromConfig($request, $config);

        $this->assertEquals(1, $collection->count());
    }

    public function testWithMultipleNames(): void
    {
        $config = [
            'releases'      => [
                'orders.index|orders.show' => [
                    '<=2' => [
                        ExamplePrepareParameterException::class,
                    ],
                ],
                'default'                  => [],
            ],
            'route_matcher' => RouteNameMatcher::class,
        ];

        $request = new Request();
        $request->server->set('REQUEST_URI', '/v2/orders');
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', 'the_route', []))->bind($request)->name('orders.index');
        });

        $collection = MiddlewareCollection::createFromConfig($request, $config);

        $this->assertEquals(1, $collection->count());
    }

    public function testNoVersionPrefix(): void
    {
        $config = [
            'releases'      => [
                'orders.index|orders.show' => [
                    '<=2' => [
                        ExamplePrepareParameterException::class,
                    ],
                ],
                'default'                  => [],
            ],
            'route_matcher' => RouteNameMatcher::class,
        ];

        $request = new Request();
        $request->server->set('REQUEST_URI', '/v2/orders');
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', 'the_route', []))->bind($request)->name('no_version.orders.index');
        });

        $collection = MiddlewareCollection::createFromConfig($request, $config);

        $this->assertEquals(1, $collection->count());
    }
}
