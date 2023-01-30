<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentAccounting;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserTwo;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\Common\Asserted;
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

    public function testListUsers(): void
    {
        $this->loadKernelBrowserAndFixtures(
            [UserAdmin::class, UserOne::class, UserTwo::class, DepartmentAccounting::class]
        );
        $department = $this->getReference(DepartmentAccounting::class, Department::class);
        $response = $this->jsonGet(
            sprintf("/departments/%d/users", Asserted::notNull($department->getId())),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
        );
        $content = $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertEquals([
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
