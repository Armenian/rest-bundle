<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

/**
 * PUT Route annotation class.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Put extends Route
{
    public function getMethod(): string
    {
        return 'PUT';
    }
}
