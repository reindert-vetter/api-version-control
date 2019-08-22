<?php
declare(strict_types=1);


namespace ReindertVetter\ApiVersionControl\Tests\Unit\VersionParser;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ReindertVetter\ApiVersionControl\Helper\VersionFromHeader;

class VersionFromHeaderTest extends TestCase
{
    /**
     * @param string $acceptHeader
     * @param string $expectVersion
     *
     * @dataProvider expectVersion
     */
    public function testGetVersion(string $acceptHeader, string $expectVersion): void
    {
        $request = new Request();
        $request->headers->set('accept', $acceptHeader);
        $versionFromHeader = new VersionFromHeader($request);

        $this->assertSame($expectVersion, $versionFromHeader->getVersion());
    }

    public function expectVersion(): array
    {
        return [
            [
                'version=0',
                '0',
            ],
            [
                '',
                '0',
            ],
            [
                'version=1.1',
                '1.1',
            ],
            [
                'text/html, application/xhtml+xml, application/xml;q=0.9, image/webp, */*;q=0.8',
                '0',
            ],
            [
                'application/json;q=0.8;version=1.1',
                '1.1',
            ],
            [
                'application/json;q=0.8;version=v1.1',
                '1.1',
            ],
            [
                'application/json;version=1.1;q=0.8',
                '1.1',
            ],
            [
                'application/json;version=1.2.3.4;q=0.8',
                '1.2.3.4',
            ],
        ];
    }
}
