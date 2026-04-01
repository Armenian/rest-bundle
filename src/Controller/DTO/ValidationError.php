<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

class ValidationError extends Error
{
    public function __construct(public string $field, public array $parameters, string $message)
    {
        parent::__construct($message);
    }
}
