<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

trait ApiTestTrait
{
    protected $acceptedJsonContentTypes = [
        'application/json',
        'application/json; charset=utf-8',
        'application/problem+json; charset=utf-8',
    ];

    protected $acceptedJsonLdContentTypes = [
        'application/ld+json',
        'application/ld+json; charset=utf-8',
        'application/problem+json; charset=utf-8',
    ];

    protected function jsonGet(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = []
    ): Response {
        return $this->jsonRequest($client, Request::METHOD_GET, $uri, $parameters, $headers);
    }

    protected function jsonPut(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        return $this->jsonRequest($client, Request::METHOD_PUT, $uri, $parameters, $headers, $content, $files);
    }

    protected function jsonPost(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        return $this->jsonRequest($client, Request::METHOD_POST, $uri, $parameters, $headers, $content, $files);
    }

    protected function jsonDelete(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null
    ): Response {
        return $this->jsonRequest($client, Request::METHOD_DELETE, $uri, $parameters, $headers, $content);
    }

    protected function jsonRequest(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        $client->request(
            $method,
            $uri,
            $parameters,
            $files,
            $this->transformJsonHeaders($headers),
            $this->jsonEncodeContent($content)
        );

        return $client->getResponse();
    }

    protected function jsonLdGet(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = []
    ): Response {
        return $this->jsonLdRequest($client, Request::METHOD_GET, $uri, $parameters, $headers);
    }

    protected function jsonLdPut(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        return $this->jsonLdRequest($client, Request::METHOD_PUT, $uri, $parameters, $headers, $content, $files);
    }

    protected function jsonLdPost(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        return $this->jsonLdRequest($client, Request::METHOD_POST, $uri, $parameters, $headers, $content, $files);
    }

    protected function jsonLdDelete(
        KernelBrowser $client,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null
    ): Response {
        return $this->jsonLdRequest($client, Request::METHOD_DELETE, $uri, $parameters, $headers, $content);
    }

    protected function jsonLdRequest(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null,
        array $files = []
    ): Response {
        $client->request(
            $method,
            $uri,
            $parameters,
            $files,
            $this->transformJsonLdHeaders($headers),
            $this->jsonEncodeContent($content)
        );

        return $client->getResponse();
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        if (Response::HTTP_NO_CONTENT !== $statusCode) {

            Assert::assertTrue(
                $this->hasJsonContentType($response),
                sprintf('JSON content type missing, given: %s', $response->headers->get('Content-Type'))
            );
        }

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        Assert::assertEquals($statusCode, $response->getStatusCode(), $content);

        return $decodedContent;
    }

    protected function assertJsonLdResponse(Response $response, $statusCode = 200)
    {
        if (Response::HTTP_NO_CONTENT !== $statusCode) {

            Assert::assertTrue(
                $this->hasJsonLdContentType($response),
                sprintf('JSON+LD content type missing, given: %s', $response->headers->get('Content-Type'))
            );
        }

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        Assert::assertEquals($statusCode, $response->getStatusCode(), $content);

        return $decodedContent;
    }

    private function hasJsonContentType($response)
    {
        foreach ($this->acceptedJsonContentTypes as $acceptedJsonContentType) {
            if ($response->headers->contains('Content-Type', $acceptedJsonContentType)) {
                return true;
            }
        }

        return false;
    }

    private function hasJsonLdContentType($response)
    {
        foreach ($this->acceptedJsonLdContentTypes as $acceptedJsonLdContentType) {
            if ($response->headers->contains('Content-Type', $acceptedJsonLdContentType)) {
                return true;
            }
        }

        return false;
    }

    protected function jsonEncodeContent(?array $content): ?string
    {
        if (null === $content) {
            return null;
        }

        return json_encode($content);
    }

    protected function transformJsonHeaders(array $headers)
    {
        $transformedHeaders = [
            'HTTP_ACCEPT'  => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];
        foreach ($headers as $key => $value) {
            if (strpos($key, 'PHP_') !== 0) {
                $transformedHeaders['HTTP_' . $key] = $value;
            } else {
                $transformedHeaders[$key] = $value;
            }
        }

        return $transformedHeaders;
    }

    protected function transformJsonLdHeaders(array $headers)
    {
        $transformedHeaders = [
            'HTTP_ACCEPT'  => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
        foreach ($headers as $key => $value) {
            if (strpos($key, 'PHP_') !== 0) {
                $transformedHeaders['HTTP_' . $key] = $value;
            } else {
                $transformedHeaders[$key] = $value;
            }
        }

        return $transformedHeaders;
    }

    protected function addJwtAuthorizationHeader(UserInterface $user, array $headers = [])
    {
        $token = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
        $headers['Authorization'] = 'Bearer ' . $token;

        return $headers;
    }

    protected function addBasicAuthorizationHeader(UserInterface $user, array $headers = []): array
    {
        $headers['PHP_AUTH_USER'] = $user->getUsername();
        $headers['PHP_AUTH_PW'] = $user->getUsername();

        return $headers;
    }

    protected function assertArrayHasKeyAndUnset($key, &$array, $message = '')
    {
        Assert::assertArrayHasKey($key, $array, $message);
        $value = $array[$key];
        unset($array[$key]);

        return $value;
    }

    protected abstract function getContainer(): ContainerInterface;
}
