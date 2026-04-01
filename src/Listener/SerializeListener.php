<?php

declare(strict_types=1);

namespace DMP\RestBundle\Listener;

use DMP\RestBundle\Annotation\Serializable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SerializeListener implements EventSubscriberInterface
{
    private const int DEFAULT_CODE = Response::HTTP_OK;
    private const string DEFAULT_FORMAT = 'json';

    private const array DEFAULT_CONTEXT = [
        JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
        XmlEncoder::ENCODING => 'UTF-8',
    ];

    public function __construct(private SerializerInterface $serializer)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => ['onKernelControllerArguments', 8],
            KernelEvents::VIEW => ['onKernelView', 35]
        ];
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $request = $event->getRequest();
        $config = $request->attributes->get(Serializable::ATTR_KEY);

        if (!$config instanceof Serializable) {
            $attributes = $event->getAttributes()[Serializable::class] ?? [];
            $config = $attributes[0] ?? null;
        }

        if (!$config instanceof Serializable) {
            return;
        }

        $request->attributes->set(Serializable::ATTR_KEY, $config);
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $config = $request->attributes->get(Serializable::ATTR_KEY);

        if (! $config instanceof Serializable || $event->getResponse()) {
            return;
        }

        $format = $request->getRequestFormat($request->getContentTypeFormat()) ?? self::DEFAULT_FORMAT;
        if (! $format) {
            return;
        }

        $result = $event->getControllerResult();

        $code = $config->getStatusCode() ?? self::DEFAULT_CODE;
        $type = "application/" . $format;

        $content = $this->serializer->serialize(
            $result,
            $format,
            array_merge(self::DEFAULT_CONTEXT, ['groups' => $config->getGroups()])
        );

        $event->setResponse(new Response($content, $code, ['content-type' => $type]));
    }
}
