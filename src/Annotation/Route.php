<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/**
 * Route annotation class.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route extends BaseRoute
{
    public function __construct(
        $data = [],
        $path = null,
        string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        string $host = null,
        $methods = [],
        $schemes = [],
        string $condition = null,
        int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null,
        string $env = null
    ) {
        parent::__construct(
            $data,
            $path,
            $name,
            $requirements,
            $options,
            $defaults,
            $host,
            $methods,
            $schemes,
            $condition,
            $priority,
            $locale,
            $format,
            $utf8,
            $stateless,
            $env
        );

        if (!$this->getMethods()) {
            $this->setMethods((array) $this->getMethod());
        }
    }

    public function getMethod(): ?string
    {
        return null;
    }
}
