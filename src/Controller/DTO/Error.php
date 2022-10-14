<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

class Error
{
    public function __construct(
        public string $message)
    {}
}
