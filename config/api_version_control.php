<?php
declare(strict_types=1);

use ReindertVetter\ApiVersionControl\Helper\RouteNameMatcher;
use ReindertVetter\ApiVersionControl\Helper\VersionFromHeader;
use ReindertVetter\ApiVersionControl\Middleware\Version\{
    ExamplePrepareParameterException,
    ExamplePrepareSortParameter,
    ExampleThrowHumanException
};

return [

    'releases' => [

        'orders.index' => [
            '<=1' => [
                ExamplePrepareParameterException::class,
            ],
        ],

        'orders.update' => [
            '<=2' => [
                ExampleThrowHumanException::class,
            ],
            '<=1' => [
                ExamplePrepareParameterException::class,
            ],
        ],

        'default' => [
            '<=2' => [
                ExampleThrowHumanException::class,
            ],
        ],

        'all' => [
            '<=1.0' => [
                ExamplePrepareSortParameter::class,
            ],
        ],

    ],

    'route_matcher' => RouteNameMatcher::class,
//    'route_matcher' => \ReindertVetter\ApiVersionControl\Helper\RouteRegexMatcher::class,

    'version_parser' => VersionFromHeader::class,
    // 'version_parser' =>  \ReindertVetter\ApiVersionControl\Helper\VersionFromUri::class,

];
