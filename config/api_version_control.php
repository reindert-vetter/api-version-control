<?php
declare(strict_types=1);

use ReindertVetter\ApiVersionControl\Helper\VersionFromHeader;
use ReindertVetter\ApiVersionControl\Middleware\Version\{
    ExamplePrepareParameterException,
    ExampleThrowHumanException
};

return [

    'releases' => [

        'GET/orders' => [
            '<=1.0' => [
                ExamplePrepareParameterException::class,
            ],
        ],

        '(POST|PUT)/orders' => [
            '<=2.0' => [
                ExampleThrowHumanException::class,
            ],
            '<=1.0' => [
                ExamplePrepareParameterException::class,
            ],
        ],

        'default' => [
            '<=2.0' => [
                ExampleThrowHumanException::class,
            ],
        ],

    ],

    'version_parser' => VersionFromHeader::class,
    // 'version_parser' =>  \ReindertVetter\ApiVersionControl\Helper\VersionFromUri::class,

];
