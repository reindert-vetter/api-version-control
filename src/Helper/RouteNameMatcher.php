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
        foreach ($names as $name) {
            if ($this->request->route()->getName() === $name) {
                return true;
            }
        }
        return false;
    }
}