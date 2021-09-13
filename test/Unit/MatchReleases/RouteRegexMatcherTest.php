<?php
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\MatchReleases;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection;
use ReindertVetter\ApiVersionControl\Middleware\Version\ExamplePrepareParameterException;

class RouteRegexMatcherTest extends TestCase
{
    public function testWithVersion(): void
    {
        $config = [
            '(GET)/orders'    => [
                '<=2' => [
                    ExamplePrepareParameterException::class,
                ],
            ],
            'default' => [],
        ];

        $request = new Request();
        $request->server->set('REQUEST_URI', '/v2/orders');
        $collection = MiddlewareCollection::createFromConfig($request, $config);

        $this->assertEquals(1, $collection->count());
    }
}
