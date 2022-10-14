<?php

declare(strict_types=1);

namespace DMP\RestBundle\Version\Resolver;

use DMP\RestBundle\Version\VersionResolverInterface;
use Symfony\Component\HttpFoundation\Request;


final class HeaderVersionResolver implements VersionResolverInterface
{

    public function __construct(
        private readonly string $headerName = 'AAA')
    {}

    public function resolve(Request $request): ?string
    {
        if (!$request->headers->has($this->headerName)) {
            return null;
        }

        return (string) $request->headers->get($this->headerName);
    }
}
