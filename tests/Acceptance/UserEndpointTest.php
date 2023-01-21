<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserUser;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Dontdrinkandroot\Common\Asserted;
use Symfony\Component\HttpFoundation\Response;

class UserEndpointTest extends AbstractAcceptanceTest
{
    public function testPostUnauthorized(): void
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $response = $this->jsonPost('/users', [], [], []);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPostForbidden(): void
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
            [],
            $this->addBasicAuthorizationHeader('admin', 'admin'),
            ['username' => 'username', 'password' => 'password', 'admin' => true]
        );
        $content = $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals([
            'id'       => 3,
            'username' => 'username',
            'roles'    => ['ROLE_USER', 'ROLE_ADMIN']
        ], $content);
    }

    public function testGet(): void
    {
        $this->loadKernelBrowserAndFixtures([Users::class]);
        $user = Asserted::instanceOf($this->referenceRepository->getReference(UserUser::class), User::class);
        $response = $this->jsonGet(
            sprintf('/users/%d', $user->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
        );
        $content = $this->assertJsonResponse($response);
        $this->assertEquals([
            'id'       => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles'    => $user->getRoles()
        ], $content);
    }
}
