<?php

declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Concerns;

trait CacheableVersion
{
    /**
     * Allow laravel to cache this class
     * @noinspection MagicMethodsValidityInspection
     */
    public static function __set_state(array $array)
    {
        return new self(...array_values($array));
    }
}
