<?php
declare(strict_types=1);

namespace DMP\RestBundle\Tests;


use DMP\RestBundle\Tests\Fixtures\Controller\TestController;
use DMP\RestBundle\Tests\Lib\RestTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    use RestTestTrait;

    private const TEST_STRING = 'testString';
    private const TEST_EMAIL = 'test@example.com';
    private const TEST_SUB_STRING = 'testSubString';
    private const TEST_SUB_INT = 42;
    private const TEST_SUB_STRING_1 = 'testSubString1';
    private const TEST_SUB_INT_1 = 1;
    private const TEST_SUB_STRING_2 = 'testSubString2';
    private const TEST_SUB_INT_2 = 2;
    private const TEST_CODE = 'testCode';
    private const VALID_REQUEST = [
        'testStringField' => self::TEST_STRING,
        'testEmailField' => self::TEST_EMAIL,
        'testSubField' => [
            'testSubStringField' => self::TEST_SUB_STRING,
            'testSubIntField' => self::TEST_SUB_INT,
        ],
        'testArrayField' => [
            [
                'testSubStringField' => self::TEST_SUB_STRING_1,
                'testSubIntField' => self::TEST_SUB_INT_1,
            ],
            [
                'testSubStringField' => self::TEST_SUB_STRING_2,
                'testSubIntField' => self::TEST_SUB_INT_2,
            ],
        ]
    ];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->setRestClient(self::createClient());
    }

    public function testRequest(): void
    {
        self::assertEquals([
            'testStringField' => self::TEST_STRING,
            'testCode' => self::TEST_CODE,
            'testEmailField' => self::TEST_EMAIL,
            'testSubField' => [
                'testSubStringField' => self::TEST_SUB_STRING,
                'testSubIntField' => self::TEST_SUB_INT,
            ],
            'testArrayField' => [
                [
                    'testSubStringField' => self::TEST_SUB_STRING_1,
                    'testSubIntField' => self::TEST_SUB_INT_1,
                ],
                [
                    'testSubStringField' => self::TEST_SUB_STRING_2,
                    'testSubIntField' => self::TEST_SUB_INT_2,
                ],
            ],
            'dateTime' => null,
            'testCollection' => null,
        ], $this->post(sprintf('/api/test/%s', self::TEST_CODE),
            202, self::VALID_REQUEST));
    }

    public function testRuntimeException(): void
    {
        self::assertEquals([
            'errors' => [[
                'message' => 'test runtime exception',
            ]],
        ], $this->get('/api/test/exception/runtime', 500));
    }

    public function testValidationError(): void
    {
        $invalidRequest = self::VALID_REQUEST;
        unset($invalidRequest['testStringField']);
        $invalidRequest['testEmailField'] = 'not-an-email';

        self::assertEquals([
            'errors' => [
                [
                    'message' => 'BLANK_VALIDATION_MESSAGE',
                    'field' => 'testStringField',
                    'type' => 'body'
                ],
                [
                    'message' => 'EMAIL_VALIDATION_MESSAGE {{ value }}',
                    'field' => 'testEmailField',
                    'type' => 'body'
                ],
            ],
        ], $this->post('/api/test/code', 400, $invalidRequest));
    }

    public function testArrayValidationError(): void
    {
        $invalidRequest = self::VALID_REQUEST;
        unset($invalidRequest['testArrayField'][1]['testSubStringField']);

        self::assertEquals([
            'errors' => [
                [
                    'message' => 'BLANK_SUB_VALIDATION_MESSAGE',
                    'field' => 'testArrayField[1].testSubStringField',
                    'type' => 'body'
                ]
            ],
        ], $this->post('/api/test/code', 400, $invalidRequest));
    }

    public function testSubValidationError(): void
    {
        $invalidRequest = self::VALID_REQUEST;
        unset($invalidRequest['testSubField']['testSubStringField']);

        self::assertEquals([
            'errors' => [
                [
                    'message' => 'BLANK_SUB_VALIDATION_MESSAGE',
                    'field' => 'testSubField.testSubStringField',
                    'type' => 'body'
                ]
            ],
        ], $this->post('/api/test/code', 400, $invalidRequest));
    }

    public function testNotFound(): void
    {
        self::assertEquals([
            'errors' => [[
                'message' => 'No route found for "POST /api/_void_"',
            ]]
        ], $this->post('/api/_void_', 404));
    }

    public function testNotFoundException(): void
    {
        self::assertEquals([
            'errors' => [[
                'message' => 'Test',
            ]]
        ], $this->post('/api/test/exception/not-found', 404));
    }

    public function testValidDateTime(): void
    {
        $validRequestWithDateTime = self::VALID_REQUEST;
        $validRequestWithDateTime['dateTime'] = 1580608940;

        self::assertEquals([
            'testStringField' => self::TEST_STRING,
            'testCode' => self::TEST_CODE,
            'testEmailField' => self::TEST_EMAIL,
            'testSubField' => [
                'testSubStringField' => self::TEST_SUB_STRING,
                'testSubIntField' => self::TEST_SUB_INT,
            ],
            'testArrayField' => [
                [
                    'testSubStringField' => self::TEST_SUB_STRING_1,
                    'testSubIntField' => self::TEST_SUB_INT_1,
                ],
                [
                    'testSubStringField' => self::TEST_SUB_STRING_2,
                    'testSubIntField' => self::TEST_SUB_INT_2,
                ],
            ],
            'dateTime' => 1580608940,
            'testCollection' => null,
        ], $this->post(sprintf('/api/test/%s', self::TEST_CODE),
            202, $validRequestWithDateTime));
    }

    public function testInvalidDateTime(): void
    {
        $invalid = 'invalid';
        $invalidRequestWithEnum = self::VALID_REQUEST;
        $invalidRequestWithEnum['dateTime'] = $invalid;

        self::assertEquals([
            'errors' => [
                [
                    'message' => sprintf('Invalid datetime "%s", expected one of the format "U".', $invalid),
                ]
            ],
        ], $this->post(sprintf('/api/test/%s', self::TEST_CODE),
            400, $invalidRequestWithEnum));
    }

    public function testCollection(): void
    {
        $collection = [
            [
                'testSubStringField' => self::TEST_SUB_STRING_1,
                'testSubIntField' => self::TEST_SUB_INT_1,
            ],
            [
                'testSubStringField' => self::TEST_SUB_STRING_2,
                'testSubIntField' => self::TEST_SUB_INT_2,
            ],
        ];
        $validRequestWithCollection = self::VALID_REQUEST;
        $validRequestWithCollection['testCollection'] = $collection;

        self::assertEquals([
            'testStringField' => self::TEST_STRING,
            'testCode' => self::TEST_CODE,
            'testEmailField' => self::TEST_EMAIL,
            'testSubField' => [
                'testSubStringField' => self::TEST_SUB_STRING,
                'testSubIntField' => self::TEST_SUB_INT,
            ],
            'testArrayField' => [
                [
                    'testSubStringField' => self::TEST_SUB_STRING_1,
                    'testSubIntField' => self::TEST_SUB_INT_1,
                ],
                [
                    'testSubStringField' => self::TEST_SUB_STRING_2,
                    'testSubIntField' => self::TEST_SUB_INT_2,
                ],
            ],
            'dateTime' => null,
            'testCollection' => [
                [
                    'testSubStringField' => self::TEST_SUB_STRING_1,
                    'testSubIntField' => self::TEST_SUB_INT_1,
                ],
                [
                    'testSubStringField' => self::TEST_SUB_STRING_2,
                    'testSubIntField' => self::TEST_SUB_INT_2,
                ],
            ],
        ], $this->post(sprintf('/api/test/%s', self::TEST_CODE),
            202, $validRequestWithCollection));
    }
}
