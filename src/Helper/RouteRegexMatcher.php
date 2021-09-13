<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

use Illuminate\Http\Request;

class RouteRegexMatcher implements RouteMatcher
{
    private $methodWithUri;

    public function __construct(Request $request)
    {

        // Remove version from uri
        $uri = preg_replace('#(/v[0-9.]+)?#i', '', $request->getPathInfo());

        $this->methodWithUri = $request->method() . $uri;
    }

    public function match(string $key): bool
    {
        return (bool)preg_match("#$key#i", $this->methodWithUri);
    }
}
