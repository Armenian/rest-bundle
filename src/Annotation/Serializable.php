<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Serializable
{
    public const string ATTR_KEY = '_serializable';

    public function __construct(
        private ?int $statusCode = null,
        private array $groups = []
    ) {
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}
