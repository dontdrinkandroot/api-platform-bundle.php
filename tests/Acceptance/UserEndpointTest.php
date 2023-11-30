<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentManagement;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserEndpointTest extends AbstractAcceptanceTest
{
    public function testPostUnauthorized(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost($client, '/users', [], [], []);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPostForbidden(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost(
            $client,
            '/users',
            [],
            $this->addBasicAuthorizationHeader(UserOne::USERNAME, UserOne::PASSWORD),
            []
        );
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPostWithMissingFields(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost($client, '/users', [], $this->addBasicAuthorizationHeader('admin', 'admin'), []);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPost(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $department = $referenceRepository->getReference(DepartmentManagement::class, Department::class);
        $response = $this->jsonPost(
            $client,
            '/users',
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            [
                'department' => sprintf("%s/departments/%s", $this->getApiPrefix(), $department->getId()),
                'username' => 'username',
                'password' => 'password',
                'admin' => true
            ]
        );
        $content = $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals([
            'id' => 3,
            'username' => 'username',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN']
        ], $content);
    }

    public function testGet(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $user = $referenceRepository->getReference(UserOne::class, User::class);
        $response = $this->jsonGet(
            $client,
            sprintf('/users/%d', $user->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
        );
        $content = $this->assertJsonResponse($response);
        $this->assertEquals([
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ], $content);
    }
}
