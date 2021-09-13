<?php
declare(strict_types=1);

namespace ReindertVetter\ApiVersionControl\Collection;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ReindertVetter\ApiVersionControl\Exception\BadConfigVersionException;
use ReindertVetter\ApiVersionControl\Helper\RouteRegexMatcher;

class MiddlewareCollection extends Collection
{
    const REGEX_VERSION_RULE = '([<>=]*)([\d.]*)';

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new collection.
     *
     * @param array                    $items
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($items = [], Request $request = null)
    {
        parent::__construct($items);
        $this->request = $request ?? Container::getInstance()->make(Request::class);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection
     */
    public static function createFromConfig(Request $request, array $config = null)
    {
        $releases = $config ?? config('api_version_control.releases');
        $default  = $releases['default'];
        $matcher  = $releases['route_matcher'] ?? RouteRegexMatcher::class;
        unset($releases['default']);

        $matcher = new $matcher($request);

        foreach ($releases as $key => $versionsWithPipes) {
            if ($matcher->match($key)) {
                return new self($versionsWithPipes, $request);
            }
        }

        return new self($default, $request);
    }

    /**
     * @return \ReindertVetter\ApiVersionControl\Collection\MiddlewareCollection
     */
    public function filterByVersionCompare(): self
    {
        /** @var \ReindertVetter\ApiVersionControl\Helper\VersionParser $versionParser */
        $versionParser  = app(config('api_version_control.version_parser'));
        $requestVersion = $versionParser->getVersion();

        return $this->filter(
            function ($pipes, $rawVersionRule) use ($requestVersion) {
                $versionRule = $this->parseRawVersion($rawVersionRule);

                return version_compare($requestVersion, $versionRule['version'], $versionRule['operator']);
            }
        );
    }

    public function permitVersionStatement(): self
    {
        foreach ($this->flatten() as $pipe) {
            if (method_exists($pipe, 'permitted')) {
                /** @var \ReindertVetter\ApiVersionControl\Concerns\VersionStatement $pipe */
                /** @noinspection PhpUndefinedFieldInspection */
                $pipe->permit = true;
            }
        }

        return $this;
    }

    public function rejectNonPipe(): self
    {
        return $this->filter(
            function ($pipe) {
                return method_exists($pipe, 'handle');
            }
        );
    }

    /**
     * @param string $rawVersionRule
     *
     * @return array
     * @throws \ReindertVetter\ApiVersionControl\Exception\BadConfigVersionException
     */
    private function parseRawVersion(string $rawVersionRule): array
    {
        preg_match('/' . self::REGEX_VERSION_RULE . '/', $rawVersionRule, $result);

        if ($rawVersionRule !== $result[0]) {
            throw new BadConfigVersionException("Version $rawVersionRule didn't match " . self::REGEX_VERSION_RULE);
        }

        return [
            'operator' => $result[1],
            'version'  => $result[2],
        ];
    }
}
