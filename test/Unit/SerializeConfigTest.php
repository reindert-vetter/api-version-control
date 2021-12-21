<?php

declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Middleware\Version\Bind;

class SerializeConfigTest extends TestCase
{
    public function testSerializeVersion(): void
    {
        $result = Bind::__set_state([
            'abstract' => 'App\\Http\\TheAbstractClass',
            'concrete'  => 'App\\Http\\TheConcreteClass',
        ]);

        self::assertEquals(new Bind('App\\Http\\TheAbstractClass', 'App\\Http\\TheConcreteClass'), $result);
    }
}
