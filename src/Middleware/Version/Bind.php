<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Illuminate\Http\Request;

class Bind
{
    private $abstract;
    private $conrete;

    public function __construct($abstract, $conrete)
    {
        $this->abstract = $abstract;
        $this->conrete = $conrete;
    }

    public function handle(Request $request, Closure $next)
    {
        app()->bind($this->abstract, $this->conrete);

        return $next($request);
    }
}
