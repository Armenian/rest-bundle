<?php

declare(strict_types=1);

namespace DMP\RestBundle;

use DMP\RestBundle\DependencyInjection\RestExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestBundle extends Bundle
{

    public function getContainerExtensionClass(): string
    {
        return RestExtension::class;
    }

    public function getContainerExtension(): RestExtension
    {
        return new RestExtension();
    }
}
