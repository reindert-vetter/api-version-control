<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;


interface VersionParser
{
    const LOWEST_VERSION = '0';

    /**
     * @return string
     */
    public function getVersion(): string;
}