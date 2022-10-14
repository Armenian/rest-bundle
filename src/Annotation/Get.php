<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

/**
 * GET Route annotation class.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Get extends Route
{
    public function getMethod(): string
    {
        return 'GET';
    }
}
