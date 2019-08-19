<?php

namespace ReindertVetter\ApiVersionControl;

use Illuminate\Support\ServiceProvider;
use ReindertVetter\ApiVersionControl\Middleware\ApiVersionControl;

/**
 * @codeCoverageIgnore
 */
class ApiVersionControlServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the configuration path
        $this->publishes(
            [
                __DIR__ . '/../config/api_version_control.php' => config_path('api_version_control.php'),
            ],
            'api-version-control-config'
        );

//        $this->app->middleware(
//            [
//                ApiVersionControl::class,
//            ]
//        );
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('api', ApiVersionControl::class);
    }
}
