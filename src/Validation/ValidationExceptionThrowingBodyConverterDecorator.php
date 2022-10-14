<?php

declare(strict_types=1);

namespace DMP\RestBundle\Validation;


use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionThrowingBodyConverterDecorator implements ParamConverterInterface
{
    public const VALIDATION_ERRORS_ARGUMENT_NAME = 'validationErrors';

    public function __construct(
        private readonly ParamConverterInterface $delegate,
        private readonly ValidationExceptionFactory $validationErrorsExceptionConverter)
    {}

    /**
     * @throws Exception
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {

        $delegateApplied = $this->delegate->apply($request, $configuration);
        if ($delegateApplied) {
            $validationErrors = $request->attributes->get(self::VALIDATION_ERRORS_ARGUMENT_NAME);
            if ($validationErrors instanceof ConstraintViolationListInterface &&
                $validationErrors->count()
            ) {
                throw $this->validationErrorsExceptionConverter->buildFromViolationsList(
                    $validationErrors,
                    ValidationErrorType::BODY
                );
            }
        }
        return $delegateApplied;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $this->delegate->supports($configuration);
    }
}
