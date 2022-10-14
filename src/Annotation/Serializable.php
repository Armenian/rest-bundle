<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Attribute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
final class Serializable implements ConfigurationInterface
{

    public const ATTR_KEY = '_serializable';

    private ?int $statusCode;
    private array $groups = [];

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
