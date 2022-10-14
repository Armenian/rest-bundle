<?php

declare(strict_types=1);


namespace DMP\RestBundle\Version;

use Symfony\Component\HttpFoundation\Request;

final class ChainVersionResolver implements VersionResolverInterface
{
    private array $resolvers = [];

    /**
     * @var array|VersionResolverInterface[]
     */
    public function __construct(array $resolvers)
    {
        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver);
        }
    }

    public function resolve(Request $request): ?string
    {
        foreach ($this->resolvers as $resolver) {
            $version = $resolver->resolve($request);

            if (null !== $version) {
                return $version;
            }
        }

        return null;
    }

    public function addResolver(VersionResolverInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }
}
