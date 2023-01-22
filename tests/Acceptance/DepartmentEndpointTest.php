<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Symfony\Component\HttpFoundation\Response;

class DepartmentEndpointTest extends AbstractAcceptanceTest
{
    public function testPostWithMissingFields(): void
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost(
            '/departments',
            [],
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            []
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPost(): void
    {
        $this->loadKernelBrowserAndFixtures([UserAdmin::class]);
        $response = $this->jsonPost(
            '/departments',
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            [
                'name' => 'Test Department',
            ]
        );
        $content = $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals([
            'id' => 2,
            'name' => 'Test Department'
        ], $content);
    }
}
