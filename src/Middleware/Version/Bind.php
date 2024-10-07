<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use ReindertVetter\ApiVersionControl\Concerns\CacheableVersion;
use Illuminate\Http\Resources\Json\JsonResource;

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
        if (is_subclass_of($this->concrete, ResourceCollection::class)){
            app()->bind($this->abstract, function(Container $container){
                return new $this->concrete(new Collection);
            });
        } elseif (is_subclass_of($this->concrete, JsonResource::class)){
            app()->bind($this->abstract, function(Container $container){
                return new $this->concrete(new \stdClass);
            });
        } else {
            app()->bind($this->abstract, $this->concrete);
        }

        return $next($request);
    }
}
