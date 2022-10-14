<?php

declare(strict_types=1);


namespace DMP\RestBundle\Tests\Lib;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;

trait RestTestTrait
{
    private AbstractBrowser $restClient;
    private ?string $authToken = null;
    private string $authPrefix = 'Bearer ';

    public function setRestClient(AbstractBrowser $restClient): void
    {
        $this->restClient = $restClient;
    }

    public function getRestClient(): AbstractBrowser
    {
        return $this->restClient;
    }

    public function setRestAuthToken(?string $token): void
    {
        $this->authToken = $token;
    }

    public function getRestAuthToken(): string
    {
        return $this->authToken;
    }

    public function setRestAuthPrefix(string $authPrefix): void
    {
        $this->authPrefix = $authPrefix;
    }

    public function getRestAuthPrefix(): string
    {
        return $this->authPrefix;
    }

    public function post(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): array
    {
        return JsonHelper::decode($this->postRaw($uri,
            $expectedStatusCode,
            $data,
            $dataAsParameters,
            $headers,
            $files,
            $authToken));
    }

    public function postRaw(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): string
    {
        return $this->makeRestRequest(
            'POST',
            $uri,
            $data,
            $expectedStatusCode,
            $dataAsParameters,
            $headers,
            $files,
            $authToken
        );
    }

    public function put(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): array
    {
        return JsonHelper::decode($this->putRaw($uri,
            $expectedStatusCode,
            $data,
            $dataAsParameters,
            $headers,
            $files,
            $authToken));
    }

    public function putRaw(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): string
    {
        return $this->makeRestRequest(
            'PUT',
            $uri,
            $data,
            $expectedStatusCode,
            $dataAsParameters,
            $headers,
            $files,
            $authToken
        );
    }

    public function delete(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): array
    {
        return JsonHelper::decode($this->deleteRaw($uri,
            $expectedStatusCode,
            $data,
            $dataAsParameters,
            $headers,
            $files,
            $authToken));
    }

    public function deleteRaw(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): string
    {
        return $this->makeRestRequest(
            'DELETE',
            $uri,
            $data,
            $expectedStatusCode,
            $dataAsParameters,
            $headers,
            $files,
            $authToken
        );
    }

    public function get(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): array
    {
        return JsonHelper::decode($this->getRaw($uri,
            $expectedStatusCode,
            $data,
            $dataAsParameters,
            $headers,
            $files,
            $authToken));
    }

    public function getRaw(
        string $uri,
        int $expectedStatusCode,
        array $data = null,
        bool $dataAsParameters = false,
        array $headers = [],
        array $files = [],
        string $authToken = null
    ): string
    {
        return $this->makeRestRequest(
            'GET',
            $uri,
            $data,
            $expectedStatusCode,
            $dataAsParameters,
            $headers,
            $files,
            $authToken
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $data
     * @param int $expectedStatusCode
     * @param bool $dataAsParameters
     * @param array $headers
     * @param array $files
     * @param string|null $authToken
     * @return string
     * @throws null
     */
    private function makeRestRequest(
        string $method,
        string $url,
        ?array $data,
        int $expectedStatusCode,
        bool $dataAsParameters,
        array $headers,
        array $files,
        ?string $authToken
    ): string
    {
        $parameters = [];
        $body = null;
        if ($dataAsParameters) {
            $parameters = $data ?? [];
        } else if ($data !== null) {
            $headers['CONTENT_TYPE'] = 'application/json';
            $body = JsonHelper::encode($data);
        }

        if ($authToken === null && $this->authToken !== null) {
            $authToken = $this->authToken;
        }

        if ($authToken !== null) {
            $headers['HTTP_AUTHORIZATION'] = $this->authPrefix . $authToken;
        }

        $this->getRestClient()->request($method,
            $url,
            $parameters,
            $files,
            $headers,
            $body
        );

        Assert::assertEquals($expectedStatusCode, $this->getRestClient()->getResponse()->getStatusCode(),
            $this->getRestClient()->getResponse()->getContent());

        return $this->getRestClient()->getResponse()->getContent();
    }
}
