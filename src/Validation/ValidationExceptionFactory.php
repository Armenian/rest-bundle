<?php

declare(strict_types=1);

namespace DMP\RestBundle\Validation;


use DMP\RestBundle\Controller\DTO\ValidationError;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionFactory
{
    public function buildFromViolationsList(ConstraintViolationListInterface $violationList,
                                                ValidationErrorType $type): ValidationException
    {
        return new ValidationException(array_map(
            fn(ConstraintViolationInterface $violation) => new ValidationError(
                $violation->getPropertyPath(),
                $violation->getParameters(),
                $type,
                $violation->getMessageTemplate()??$violation->getMessage(),
            ),
            iterator_to_array($violationList)
        ));
    }
}
