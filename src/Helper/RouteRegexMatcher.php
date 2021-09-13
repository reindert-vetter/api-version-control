<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

use Illuminate\Http\Request;

/**
 * You can authorize the versions by means of a regex. This is prone to bugs. Documented in an old readme version:
 * @see https://github.com/reindert-vetter/api-version-control/blob/8fbd3b86ee2b3f2154a5ef2d46877d32d980ea8c/README.md#releases
 */
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
