<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Symfony\Component\HttpFoundation\Response;

class UserEndpointTest extends AbstractAcceptanceTest
{
    public function testPostUnauthorized()
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost('/users', [], [], []);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPostForbidden()
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost('/users', [], $this->addBasicAuthorizationHeader('user', 'user'), []);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPostWithMissingFields(): void
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost('/users', [], $this->addBasicAuthorizationHeader('admin', 'admin'), []);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost(
            '/users',
            ['username' => 'username', 'password' => 'password'],
            $this->addBasicAuthorizationHeader('admin', 'admin'),
            []
        );
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }
}
