<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Symfony\Component\Routing\Attribute\Route as BaseRoute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route extends BaseRoute
{
    public function __construct(
        array|string $data = [],
        array|string|null $path = null,
        string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        string $condition = null,
        int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null,
        string $env = null
    ) {
        if (\is_string($data)) {
            $data = ['path' => $data];
        } elseif (!\is_array($data)) {
            throw new \TypeError(sprintf('"%s": Argument $data is expected to be a string or array, got "%s".', __METHOD__, get_debug_type($data)));
        } elseif (0 !== count($data) && [] === \array_intersect(\array_keys($data), ['path', 'name', 'requirements', 'options', 'defaults', 'host', 'methods', 'schemes', 'condition', 'priority', 'locale', 'format', 'utf8', 'stateless', 'env'])) {
            $localizedPaths = $data;
            $data = ['path' => $localizedPaths];
        }

        parent::__construct(
            $data['path'] ?? $path,
            $data['name'] ?? $name,
            $data['requirements'] ?? $requirements,
            $data['options'] ?? $options,
            $data['defaults'] ?? $defaults,
            $data['host'] ?? $host,
            $data['methods'] ?? $methods,
            $data['schemes'] ?? $schemes,
            $data['condition'] ?? $condition,
            $data['priority'] ?? $priority,
            $data['locale'] ?? $locale,
            $data['format'] ?? $format,
            $data['utf8'] ?? $utf8,
            $data['stateless'] ?? $stateless,
            $data['env'] ?? $env
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
