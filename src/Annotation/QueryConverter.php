<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

use Attribute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Annotation
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
final class QueryConverter extends ParamConverter
{

    private const CONVERTER = 'dmp_rest.request_query';

    public function __construct(
        $data = [],
        string $class = null,
        array $options = [],
        bool $isOptional = false,
        string $converter = null
    ) {
        parent::__construct($data, $class, $options, $isOptional, ($converter !== null ? $converter : self::CONVERTER));
    }
}
