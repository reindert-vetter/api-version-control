<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Helper;

interface RouteMatcher
{
    public function match(string $key): bool;
}
