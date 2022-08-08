<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

use Illuminate\Http\Request;

class VersionFromUri implements VersionParser
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
        $uri = $this->request->getPathInfo();
        if (! preg_match('/\/v([0-9.]+)\/?/i', $uri, $version)) {
            return self::LOWEST_VERSION;
        };

        return $version[1];
    }
}
