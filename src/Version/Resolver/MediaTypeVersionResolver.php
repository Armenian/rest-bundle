<?php

declare(strict_types=1);

namespace DMP\RestBundle\Version\Resolver;

use DMP\RestBundle\Version\VersionResolverInterface;
use Symfony\Component\HttpFoundation\Request;


final class MediaTypeVersionResolver implements VersionResolverInterface
{
    public function __construct(
        private readonly string $regex)
    {}

    public function resolve(Request $request): ?string
    {
        if (!$request->attributes->has('media_type')
                || false === preg_match($this->regex, $request->attributes->get('media_type'), $matches)) {
            return null;
        }

        return $matches['version'] ?? null;
    }
}
