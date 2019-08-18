<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

use Illuminate\Http\Request;

class VersionFromHeader implements VersionParser
{
    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * VersionFromHeader constructor.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        $acceptHeader = $this->request->header('accept');
        if (! $acceptHeader) {
            return self::LOWEST_VERSION;
        }

        if (! preg_match('/version=([0-9\.]+)/i', $acceptHeader, $version)) {
            return self::LOWEST_VERSION;
        }

        return $version[1];
    }
}