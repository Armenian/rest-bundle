<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures\Controller;


use DMP\RestBundle\Pagination\Paginated;
use Doctrine\Common\Collections\ArrayCollection;
use DMP\RestBundle\Tests\Fixtures\RequestDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseSubDTO;
use DMP\RestBundle\Annotation as Rest;
use RuntimeException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class TestController
{
    #[Route("/api/test/{testCode}", methods: ["POST"])]
    #[Rest\Serializable(statusCode: 202)]
    public function request(
        string $testCode,
        #[MapRequestPayload] RequestDTO $request
    ): ResponseDTO {
        return $this->getResponseDtoFromRequest($testCode, $request);
    }

    #[Route("/api/paginated", methods: ["POST"])]
    #[Rest\Serializable(statusCode: 200)]
    public function paginated(#[MapRequestPayload] RequestDTO $request): Paginated
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

    #[Route("/api/test/exception/runtime", methods: ["GET"])]
    public function runExceptionPropagation(): void
    {
        throw new RuntimeException('test runtime exception', 42);
    }

    #[Route("/api/test/exception/not-found", methods: ["GET"])]
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
