<?php
/** @noinspection PhpUndefinedFieldInspection */
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\StatementVersion;

use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection;
use ReindertVetter\ApiVersionControl\Tests\Unit\StatementVersion\Mock\MockVersionStatementSecond;
use ReindertVetter\ApiVersionControl\Tests\Unit\StatementVersion\Mock\MockVersionMiddleware;
use ReindertVetter\ApiVersionControl\Tests\Unit\StatementVersion\Mock\MockVersionStatementFirst;

class VersionStatementTest extends TestCase
{
    protected function setUp()
    {
        // Reset static variable
        $goodVersionStatementMiddleware = new MockVersionStatementFirst();
        $goodVersionStatementMiddleware->permit = false;
    }

    public function testPermitVersionStatement(): void
    {
        $goodVersionStatement = new MockVersionStatementFirst();
        $middlewareCollection = new MiddlewareCollection([$goodVersionStatement]);
        $middlewareCollection->permitVersionStatement();

        $this->assertTrue(MockVersionStatementFirst::permitted());
    }

    public function testNotPermitVersionStatement(): void
    {
        $goodVersionStatement = new MockVersionStatementFirst();
        $middlewareCollection = new MiddlewareCollection([$goodVersionStatement]);
        $middlewareCollection->permitVersionStatement();

        $this->assertFalse(MockVersionStatementSecond::permitted());
    }

    public function testPermitVersionStatementStringClass(): void
    {
        $goodVersionStatement = MockVersionStatementFirst::class;
        $middlewareCollection = new MiddlewareCollection([$goodVersionStatement]);
        $middlewareCollection->permitVersionStatement();

        $this->assertFalse(MockVersionStatementSecond::permitted());
    }

    public function testRejectVersionStatement(): void
    {
        $middlewareCollection = new MiddlewareCollection(
            [new MockVersionStatementFirst(), new MockVersionMiddleware()]
        );
        $middlewareCollection = $middlewareCollection
            ->permitVersionStatement()
            ->rejectNonPipe();

        $this->assertCount(1, $middlewareCollection);
        $this->assertInstanceOf(MockVersionMiddleware::class, $middlewareCollection->first());
    }

    public function testVersionNotPermit(): void
    {
        $this->assertFalse(MockVersionStatementFirst::permitted());
    }

    public function testMiddleWareWithoutTrait(): void
    {
        $this->expectExceptionMessageRegExp('/Call to undefined method/');
        /** @noinspection PhpUndefinedMethodInspection */
        MockVersionMiddleware::permitted();
    }
}
