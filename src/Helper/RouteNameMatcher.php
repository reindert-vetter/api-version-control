<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

use Illuminate\Http\Request;

class RouteNameMatcher implements RouteMatcher
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function match(string $key): bool
    {
        $names = explode('|', $key);
        $routeName = $this->request->route()->getName()   
        if ($routeName === null) {
            return false;
        }

        $routeName = preg_replace('/^no_version\./', '', $routeName);

        foreach ($names as $name) {
            if ($routeName === $name) {
                return true;
            }
        }
        return false;
    }
}
