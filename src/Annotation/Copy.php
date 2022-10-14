<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Copy extends Route
{
    public function getMethod(): string
    {
        return 'COPY';
    }
}
