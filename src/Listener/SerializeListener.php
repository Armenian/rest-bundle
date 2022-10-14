<?php

declare(strict_types=1);

namespace DMP\RestBundle\Listener;

use DMP\RestBundle\Annotation\Serializable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializeListener implements EventSubscriberInterface
{
    private const DEFAULT_CODE = Response::HTTP_OK;

    private const DEFAULT_CONTEXT = [
        JsonEncode::OPTIONS => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
        XmlEncoder::ENCODING => 'UTF-8',
    ];

    public function __construct(
        private readonly SerializerInterface $serializer)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 35]
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $config = $request->attributes->get(Serializable::ATTR_KEY);

        if (! $config instanceof Serializable || $event->getResponse()) {
            return;
        }

        $format = $request->getRequestFormat($request->getContentType());

        if (! $format) {
            return;
        }

        $result = $event->getControllerResult();

        $code = $config->getStatusCode() ?? self::DEFAULT_CODE;
        $type = "application/json";

        $content = $this->serializer->serialize(
            $result,
            $format,
            array_merge(self::DEFAULT_CONTEXT, ['groups' => $config->getGroups()])
        );

        $event->setResponse(new Response($content, $code, ['content-type' => $type]));
    }
}
