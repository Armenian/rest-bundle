<?php

declare(strict_types=1);

namespace DMP\RestBundle\Listener;


use DMP\RestBundle\Version\VersionResolverInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class VersionListener
{
    public function __construct(
        private readonly VersionResolverInterface $versionResolver,
        private readonly ?string $defaultVersion = null)
    {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $version = $this->versionResolver->resolve($request);

        if (null === $version && null !== $this->defaultVersion) {
            $version = $this->defaultVersion;
        }

        if (null === $version) {
            return;
        }

        $request->attributes->set('version', $version);
    }
}
