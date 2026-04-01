<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Attribute;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Serializable
{

    public const string ATTR_KEY = '_serializable';

    private ?int $statusCode;
    private array $groups;

    public function __construct(
        $data = [],
        ?int $statusCode = null,
        array $groups = []
    ) {
        $values = is_array($data) ? $data : [];
        $this->statusCode = $values['statusCode'] ?? $statusCode;
        $this->groups = $values['groups'] ?? $groups;
    }

    public function getAliasName(): string
    {
        return ltrim(self::ATTR_KEY, '_');
    }

    public function allowArray(): bool
    {
        return false;
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
