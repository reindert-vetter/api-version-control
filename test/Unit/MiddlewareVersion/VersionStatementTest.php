<?php
/** @noinspection PhpUndefinedFieldInspection */
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\VersionParser;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection;
use ReindertVetter\ApiVersionControl\Concerns\VersionStatement;
use ReindertVetter\ApiVersionControl\Tests\Unit\MiddlewareVersion\Mock\GoodVersionStatement;
use ReindertVetter\ApiVersionControl\Tests\Unit\MiddlewareVersion\Mock\MockVersionMiddleware;
use ReindertVetter\ApiVersionControl\Tests\Unit\MiddlewareVersion\Mock\MockVersionStatement;

class VersionStatementTest extends TestCase
{
    protected function setUp()
    {
        // Reset static variable
        $goodVersionStatementMiddleware         = new MockVersionStatement();
        $goodVersionStatementMiddleware->permit = false;
    }

    public function testPermitVersionStatement(): void
    {

        $goodVersionStatement = new MockVersionStatement();
        $middlewareCollection = new MiddlewareCollection([$goodVersionStatement]);
        $middlewareCollection->permitVersionStatement();

        $this->assertTrue(MockVersionStatement::permitted());
    }

    public function testRejectVersionStatement(): void
    {
        $middlewareCollection = new MiddlewareCollection([new MockVersionStatement(), new MockVersionMiddleware()]);
        $middlewareCollection = $middlewareCollection
            ->permitVersionStatement()
            ->rejectNonPipe();

        $this->assertCount(1, $middlewareCollection);
        $this->assertInstanceOf(MockVersionMiddleware::class, $middlewareCollection->first());
    }

    public function testVersionNotPermit(): void
    {
        $this->assertFalse(MockVersionStatement::permitted());
    }

    public function testMiddleWareWithoutTrait(): void
    {
        $this->expectExceptionMessageRegExp('/Call to undefined method/');
        /** @noinspection PhpUndefinedMethodInspection */
        MockVersionMiddleware::permitted();
    }
}
