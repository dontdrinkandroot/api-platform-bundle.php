<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentAccounting;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserTwo;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Symfony\Component\HttpFoundation\Response;

class DepartmentEndpointTest extends AbstractAcceptanceTestCase
{
    public function testPostWithMissingFields(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost(
            $client,
            '/departments',
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            []
        );
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPost(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([UserAdmin::class]);
        $response = $this->jsonPost(
            $client,
            '/departments',
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            [
                'name' => 'Test Department',
            ]
        );
        $content = self::assertJsonResponse($response, Response::HTTP_CREATED);
        self::assertEquals([
            'id' => 2,
            'name' => 'Test Department'
        ], $content);
    }

    public function testListUsers(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures(
            [UserAdmin::class, UserOne::class, UserTwo::class, DepartmentAccounting::class]
        );
        $department = $referenceRepository->getReference(DepartmentAccounting::class, Department::class);
        $response = $this->jsonGet(
            $client,
            sprintf("/departments/%d/users", $department->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
        );
        $content = self::assertJsonResponse($response, Response::HTTP_OK);
        self::assertEquals([
            [
                'id' => 2,
                'username' => 'userone',
                'roles' => ['ROLE_USER']
            ],
            [
                'id' => 3,
                'username' => 'usertwo',
                'roles' => ['ROLE_USER']
            ],
        ], $content);
    }
}
