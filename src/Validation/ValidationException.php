<?php

declare(strict_types=1);

namespace DMP\RestBundle\Validation;

use DMP\RestBundle\Controller\DTO\ValidationError;
use RuntimeException;

class ValidationException extends RuntimeException
{

    /**
     * @param array|ValidationError[] $validationErrors
     */
    public function __construct(
        private readonly array $validationErrors)
    {
        parent::__construct('Validation error');
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
