<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;
use ReflectionClass;
use function str_contains;
use function explode;

class ExceptionDTOFactory implements ExceptionDTOFactoryInterface
{
    public function buildExceptionDTO(Throwable $exception): ExceptionDTO
    {
        $exceptionDTO = new ExceptionDTO();
        $exceptionDTO->errors[] = new Error(
            $exception->getMessage() ? $exception->getMessage() :
                (new ReflectionClass($exception->getPrevious() ?? $exception))->getShortName()
        );
        return $exceptionDTO;
    }

    public function buildExceptionDTOFromViolations(ConstraintViolationListInterface $violations): ExceptionDTO
    {
        $dto = new ExceptionDTO();
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $message = $violation->getMessageTemplate() ?? $violation->getMessage();
            if (str_contains($message, '|')) {
                $message = explode('|', $message)[1];
            }
            $dto->errors[] = new ValidationError(
                $violation->getPropertyPath(),
                $violation->getParameters(),
                $message,
            );
        }
        return $dto;
    }
}
