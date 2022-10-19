<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures\Controller;


use DMP\RestBundle\Pagination\Paginated;
use DMP\RestBundle\Pagination\Pagination;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use DMP\RestBundle\Annotation\BodyConverter;
use DMP\RestBundle\Tests\Fixtures\RequestDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseSubDTO;
use DMP\RestBundle\Annotation as Rest;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestController
{
    public const EXCEPTION_MESSAGE = 'MESSAGE';
    public const EXCEPTION_KEY = 'KEY';
    public const EXCEPTION_GROUP = 'GROUP';
    public const EXCEPTION_VALUES = [
        'foo' => 'bar',
    ];

    #[Rest\Post("/api/test/{testCode}")]
    #[Rest\Serializable(statusCode: 202)]
    #[BodyConverter("request", class: "DMP\RestBundle\Tests\Fixtures\RequestDTO")]
    public function request(string $testCode, RequestDTO $request): ResponseDTO
    {
        return $this->getResponseDtoFromRequest($testCode, $request);
    }

    #[Rest\Post("/api/paginated")]
    #[Rest\Serializable(statusCode: 200)]
    #[BodyConverter("request", class: "DMP\RestBundle\Tests\Fixtures\RequestDTO")]
    public function paginated(RequestDTO $request): Paginated
    {
        return new Paginated(
            100,
            2,
            1,
            new ArrayCollection(
                [
                    $this->getResponseDtoFromRequest('paginated', $request),
                    $this->getResponseDtoFromRequest('paginated', $request)
                ]
            ),
            50,
            null,
            2
        );
    }

    #[Rest\Get("/api/test/exception/runtime")]
    public function runExceptionPropagation(): void
    {
        throw new RuntimeException('test runtime exception', 42);
    }

    #[Rest\Get("/api/test/exception/not-found")]
    public function notFound(): void
    {
        throw new NotFoundHttpException('Test');
    }

    private function getResponseDtoFromRequest(string $testCode, RequestDTO $request): ResponseDTO
    {
        $response = new ResponseDTO();
        $response->testStringField = $request->testStringField;
        $response->testSubField = new ResponseSubDTO(
            $request->testSubField->testSubStringField,
            $request->testSubField->testSubIntField
        );
        $response->testArrayField = [
            new ResponseSubDTO(
                $request->testArrayField[0]->testSubStringField,
                $request->testArrayField[0]->testSubIntField
            ),
            new ResponseSubDTO(
                $request->testArrayField[1]->testSubStringField,
                $request->testArrayField[1]->testSubIntField
            ),
        ];
        $response->testEmailField = $request->testEmailField;
        $response->testCode = $testCode;
        $response->dateTime = $request->dateTime;
        $response->testCollection = $request->testCollection;
        return $response;
    }
}
