<?php
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\VersionParser;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Helper\VersionFromUri;

class VersionFromUriTest extends TestCase
{
    /**
     * @param string $uri
     * @param string $expectVersion
     *
     * @dataProvider expectVersion
     */
    public function testGetVersion(string $uri, string $expectVersion): void
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', $uri);
        $versionFromHeader = new VersionFromUri($request);

        $this->assertSame($expectVersion, $versionFromHeader->getVersion());
    }

    public function expectVersion(): array
    {
        return [
            [
                '/test',
                '0',
            ],
            [
                '/v3/test',
                '3',
            ],
            [
                '/v3.0/test',
                '3.0',
            ],
            [
                '/v/v3.0/test',
                '3.0',
            ],
            [
                '/v/v3.0.0/test',
                '3.0.0',
            ],
            [
                '/v/v3.0suffix/test',
                '0',
            ],
        ];
    }
}
