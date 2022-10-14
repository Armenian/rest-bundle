<?php

declare(strict_types=1);

namespace DMP\RestBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HttpException extends SymfonyHttpException
{
    public static function from(Throwable $exception, int $statusCode, array $headers = []): self
    {
        return new self(
            $statusCode,
            $exception->getMessage(),
            $exception,
            $headers,
            $exception->getCode()
        );
    }

    public static function to404(Throwable $exception): self
    {
        return self::from($exception, Response::HTTP_NOT_FOUND);
    }

    public static function to403(Throwable $exception): self
    {
        return self::from($exception, Response::HTTP_FORBIDDEN);
    }

    public static function to401(Throwable $exception, string $challenge): self
    {
        return self::from($exception, Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => $challenge
        ]);
    }

    public static function to400(Throwable $exception): self
    {
        return self::from($exception, Response::HTTP_BAD_REQUEST);
    }

    public static function to417(Throwable $exception): self
    {
        return self::from($exception, Response::HTTP_EXPECTATION_FAILED);
    }
}
