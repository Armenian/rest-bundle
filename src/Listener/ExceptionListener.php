<?php

declare(strict_types=1);

namespace DMP\RestBundle\Listener;

use DMP\RestBundle\Controller\DTO\ExceptionDTOFactoryInterface;
use DMP\RestBundle\Exception\NotFoundException;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;
use LogicException;

final readonly class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(private ExceptionDTOFactoryInterface $exceptionDTOFactory, private string $appEnv)
    {
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
        $previous = $exception->getPrevious();

        if ($exception instanceof HttpExceptionInterface && $previous instanceof ValidationFailedException) {
            return new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTOFromViolations($previous->getViolations()),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => "application/problem+json"]
            );
        }

        if ($exception instanceof HttpExceptionInterface) {
            return new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTO($exception),
                $exception->getStatusCode()
            );
        }

        return match (true) {
            $exception instanceof NotFoundException => new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTO($exception), Response::HTTP_NOT_FOUND),
            default => new JsonResponse(
                $this->exceptionDTOFactory->buildExceptionDTO($exception),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        };
    }
}
