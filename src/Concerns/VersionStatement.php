<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Concerns;

trait VersionStatement
{
    private static $permit = false;

    public static function permitted()
    {
        return self::$permit;
    }

    public function __set($name, $value)
    {
        if ($name === 'permit') {
            self::$permit = $value;
        }
    }
}