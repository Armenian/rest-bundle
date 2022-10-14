<?php

declare(strict_types=1);

namespace DMP\RestBundle\Annotation;

/**
 * OPTIONS Route annotation class.
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Options extends Route
{
    public function getMethod(): string
    {
        return 'OPTIONS';
    }
}
