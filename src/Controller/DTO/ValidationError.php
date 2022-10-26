<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;


use DMP\RestBundle\Validation\ValidationErrorType;

class ValidationError extends Error
{
    public function __construct(public string $field, public array $parameters, public ValidationErrorType $type, string $message)
    {
        parent::__construct($message);
    }
}
