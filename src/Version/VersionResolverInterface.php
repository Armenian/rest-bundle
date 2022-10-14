<?php

declare(strict_types=1);

namespace DMP\RestBundle\Version;

use Symfony\Component\HttpFoundation\Request;

interface VersionResolverInterface
{
    public function resolve(Request $request): string|null;
}
