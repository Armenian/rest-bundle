<?php

declare(strict_types=1);


namespace DMP\RestBundle\Request;

use DMP\RestBundle\Context\Context;
use DMP\RestBundle\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use InvalidArgumentException;
use Exception;

abstract class AbstractParamConverter
{
    protected array $context = [];

    public function __construct(
        protected readonly string $converter,
        protected readonly Serializer $serializer,
        ?array $groups = null,
        ?string $version = null,
        protected readonly ?ValidatorInterface $validator = null,
        protected readonly ?string $validationErrorsArgument = null)
    {
        if (!empty($groups)) {
            $this->context['groups'] = $groups;
        }

        if (!empty($version)) {
            $this->context['version'] = $version;
        }

        if (null !== $validator && null === $validationErrorsArgument) {
            throw new InvalidArgumentException('"$validationErrorsArgument" cannot be null when using the validator');
        }
    }

    public function supports(ParamConverter $configuration): bool
    {
        return null !== $configuration->getClass() && $this->converter === $configuration->getConverter();
    }

    protected function configureContext(Context $context, array $options): void
    {
        foreach ($options as $key => $value) {
            if ('groups' === $key) {
                $context->addGroups($options['groups']);
            } elseif ('version' === $key) {
                $context->setVersion($options['version']);
            } elseif ('enableMaxDepth' === $key) {
                $context->enableMaxDepth($options['enableMaxDepth']);
            } elseif ('serializeNull' === $key) {
                $context->setSerializeNull($options['serializeNull']);
            } else {
                $context->setAttribute($key, $value);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function throwException(Exception $exception, ParamConverter $configuration): bool
    {
        if ($configuration->isOptional()) {
            return false;
        }

        throw $exception;
    }

    protected function getValidatorOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'groups' => null,
            'traverse' => false,
            'deep' => false,
        ]);

        return $resolver->resolve($options['validator'] ?? []);
    }
}
