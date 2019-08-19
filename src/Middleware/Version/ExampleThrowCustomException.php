<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExampleThrowCustomException
{
    use Logger;

    /**
     * @param           $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($response->exception) {
            return response()->json(
                [
                    "errors" => [
                        [
                            "human" => $ex->getMessage(),
                        ]
                    ]
                ]
            );
        }

        return $response;
    }
}