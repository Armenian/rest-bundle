<?php

declare(strict_types=1);

namespace DMP\RestBundle\Request;

use DMP\RestBundle\Context\Context;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;
use Exception;


final class QueryParamConverter extends AbstractParamConverter implements ParamConverterInterface
{

    /**
     * @throws Exception
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $options = $configuration->getOptions();

        if (isset($options['deserializationContext']) && is_array($options['deserializationContext'])) {
            $arrayContext = array_merge($this->context, $options['deserializationContext']);
        } else {
            $arrayContext = $this->context;
        }
        $this->configureContext($context = new Context(), $arrayContext);

        try {
            $object = $this->serializer->deserialize(
                json_encode($request->query->all(), JSON_THROW_ON_ERROR),
                $configuration->getClass(),
                'json',
                $context
            );
        } catch (SymfonySerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $request->attributes->set($configuration->getName(), $object);
        if (null !== $this->validator && (!isset($options['validate']) || $options['validate'])) {
            $validatorOptions = $this->getValidatorOptions($options);

            $errors = $this->validator->validate($object, null, $validatorOptions['groups']);
            $request->attributes->set(
                $this->validationErrorsArgument,
                $errors
            );
        }

        return true;
    }

}
