<?php

declare(strict_types=1);

namespace DMP\RestBundle\Serializer;

use DMP\RestBundle\Context\Context;
use Symfony\Component\Serializer\SerializerInterface;

final class SymfonySerializerAdapter implements Serializer
{

    public function __construct(
        private readonly SerializerInterface $serializer)
    {}

    public function serialize($data, string $format, Context $context): string
    {
        $newContext = $this->convertContext($context);
        return $this->serializer->serialize($data, $format, $newContext);
    }

    public function deserialize(string $data, string $type, string $format, Context $context): mixed
    {
        $newContext = $this->convertContext($context);
        return $this->serializer->deserialize($data, $type, $format, $newContext);
    }

    private function convertContext(Context $context): array
    {
        $newContext = [];
        foreach ($context->getAttributes() as $key => $value) {
            $newContext[$key] = $value;
        }

        if (null !== $context->getGroups()) {
            $newContext['groups'] = $context->getGroups();
        }

        $newContext['version'] = $context->getVersion();
        $newContext['enable_max_depth'] = $context->isMaxDepthEnabled();
        $newContext['skip_null_values'] = !$context->getSerializeNull();

        return $newContext;
    }
}
