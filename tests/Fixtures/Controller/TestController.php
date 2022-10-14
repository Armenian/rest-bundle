<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests\Fixtures\Controller;


use Exception;
use DMP\RestBundle\Annotation\BodyConverter;
use DMP\RestBundle\Tests\Fixtures\RequestDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseDTO;
use DMP\RestBundle\Tests\Fixtures\ResponseSubDTO;
use FOS\RestBundle\Controller\Annotations as Rest;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestController
{
    public const EXCEPTION_MESSAGE = 'MESSAGE';
    public const EXCEPTION_KEY = 'KEY';
    public const EXCEPTION_GROUP = 'GROUP';
    public const EXCEPTION_VALUES = [
        'foo' => 'bar',
    ];
    /**
     * @Rest\Post("/api/test/{testCode}")
     * @Rest\View(statusCode=202)
     * @BodyConverter("request", class="DMP\RestBundle\Tests\Fixtures\RequestDTO:class")
     */
    public function request(string $testCode, RequestDTO $request): ResponseDTO
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

    /**
     * @Rest\Get("/api/test/exception/runtime")
     */
    public function runExceptionPropagation(): void
    {
        throw new RuntimeException('test runtime exception', 42);
    }

    /**
     * @Rest\Get("/api/test/exception/translatable")
     */
    public function runTranslatableException(): void
    {
        throw new class extends Exception implements TranslatableException
        {

            /**
             * AnException constructor.
             */
            public function __construct()
            {
                parent::__construct(TestController::EXCEPTION_MESSAGE);
            }

            public function getKey(): string
            {
                return TestController::EXCEPTION_KEY;
            }

            public function getGroup(): string
            {
                return TestController::EXCEPTION_GROUP;
            }

            public function getValues(): array
            {
                return TestController::EXCEPTION_VALUES;
            }
        };
    }

    /**
     * @Rest\Post("/api/test/exception/not-found")
     */
    public function notFound(): void
    {
        throw new NotFoundHttpException('Test');
    }
}
