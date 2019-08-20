<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Middleware\Version;

use Closure;
use Illuminate\Http\Request;

class ExampleThrowCustomException
{
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
            $response->setContent(
                [
                    "errors" => [
                        [
                            "human" => $response->exception->getMessage(),
                        ],
                    ],
                ]
            );
        }

        return $response;
    }
}