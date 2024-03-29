<?php

declare(strict_types=1);

namespace DMP\RestBundle\Listener;

use DMP\RestBundle\Controller\DTO\ExceptionDTOFactoryInterface;
use DMP\RestBundle\Exception\NotFoundException;
use DMP\RestBundle\Validation\ValidationException;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Throwable;
use LogicException;

class ExceptionListener  implements EventSubscriberInterface
{

    private ExceptionDTOFactoryInterface $exceptionDTOFactory;

    public function __construct(ExceptionDTOFactoryInterface $exceptionDTOFactory, private string $appEnv)
    {
        $this->exceptionDTOFactory = $exceptionDTOFactory;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof DriverException && $this->appEnv === 'prod') {
            throw new LogicException('Bad request');
        }

        if ($exception instanceof HandlerFailedException) {
            $response = $this->getResponse($exception->getPrevious());
        } else {
            $response = $this->getResponse($exception);
        }

        $event->setResponse($response);
        $event->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    private function getResponse(Throwable $exception): Response
    {
        return match (true) {
            $exception instanceof ValidationException => new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTOFromValidationException($exception),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => "application/problem+json"]),
            $exception instanceof NotFoundException => new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTO($exception), Response::HTTP_NOT_FOUND),
            default => new JsonResponse($this->exceptionDTOFactory->buildExceptionDTO($exception))
        };
    }
}
