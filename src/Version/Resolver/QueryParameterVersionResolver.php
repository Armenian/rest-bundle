<?php

declare(strict_types=1);

namespace DMP\RestBundle\Version\Resolver;

use DMP\RestBundle\Version\VersionResolverInterface;
use Symfony\Component\HttpFoundation\Request;


final class QueryParameterVersionResolver implements VersionResolverInterface
{

    public function __construct(
        private readonly string $parameterName)
    {}

    public function resolve(Request $request): ?string
    {
        if (!$request->query->has($this->parameterName)) {
            return null;
        }

        return (string) $request->query->get($this->parameterName);
    }
}
