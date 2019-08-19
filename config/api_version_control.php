<?php
declare(strict_types=1);

use ReindertVetter\ApiVersionControl\Helper\VersionFromHeader;
use ReindertVetter\ApiVersionControl\Middleware\Version\ExampleThrowCustomException;

return [

    'GET/orders' => [
        '<=1.0' => [
            ExampleThrowCustomException::class
        ],
    ],

    '(POST|PUT)/orders' => [
        '<=2.0' => [
            ExampleThrowCustomException::class
        ],
        '<=1.0' => [
            ExampleThrowCustomException::class
        ],
    ],

    'default' => [
        '<=2.0' => [
            ExampleThrowCustomException::class
        ],
    ],

    'version_parser' => VersionFromHeader::class,
    //    'version_parser' => VersionFromUri::class,

];