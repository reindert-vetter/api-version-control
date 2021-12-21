<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Illuminate\Http\Request;
use ReindertVetter\ApiVersionControl\Concerns\CacheableVersion;

class Bind
{
    use CacheableVersion;

    private $abstract;
    private $concrete;

    public function __construct($abstract, $concrete)
    {
        $this->abstract = $abstract;
        $this->concrete = $concrete;
    }

    public function handle(Request $request, Closure $next)
    {
        app()->bind($this->abstract, $this->concrete);

        return $next($request);
    }
}
