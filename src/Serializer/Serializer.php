<?php

declare(strict_types=1);

namespace DMP\RestBundle\Serializer;

use DMP\RestBundle\Context\Context;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;

interface Serializer
{
    public function serialize($data, string $format, Context $context): string;

    /**
     * @throws SymfonySerializerException
     */
    public function deserialize(string $data, string $type, string $format, Context $context): mixed;
}
