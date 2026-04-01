<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

interface ExceptionDTOFactoryInterface
{
    public function buildExceptionDTO(Throwable $exception): ExceptionDTO;
    public function buildExceptionDTOFromViolations(ConstraintViolationListInterface $violations): ExceptionDTO;
}
