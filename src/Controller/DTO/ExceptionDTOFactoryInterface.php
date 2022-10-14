<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

use DMP\RestBundle\Validation\ValidationException;
use Throwable;

interface ExceptionDTOFactoryInterface
{
    public function buildExceptionDTO(Throwable $exception): ExceptionDTO;
    public function buildExceptionDTOFromValidationException(ValidationException $exception): ExceptionDTO;
}
