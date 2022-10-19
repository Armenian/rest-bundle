<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller\DTO;

use DMP\RestBundle\Validation\ValidationException;
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
            $exception->getMessage() ? $exception->getMessage() : (new ReflectionClass($exception->getPrevious()))->getShortName()
        );
        return $exceptionDTO;
    }

    public function buildExceptionDTOFromValidationException(ValidationException $exception): ExceptionDTO
    {
        $dto = new ExceptionDTO();
        $errors = $exception->getValidationErrors();
        /** @var ValidationError $error */
        foreach($errors as $error) {
            if(str_contains($error->message, '|')) {
                $error->message = explode('|', $error->message)[1];
            }
        }
        $dto->errors = $errors;
        return $dto;
    }
}
