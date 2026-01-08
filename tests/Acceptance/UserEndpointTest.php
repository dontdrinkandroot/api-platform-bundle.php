<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentManagement;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\Users;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserEndpointTest extends AbstractAcceptanceTestCase
{
    public function testPostUnauthorized(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost($client, '/users', [], [], []);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
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
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPatchUnauthorized(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPatch($client, '/users/1', [], [], []);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPatchForbidden(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPatch(
            $client,
            '/users/1',
            [],
            $this->addBasicAuthorizationHeader(UserOne::USERNAME, UserOne::PASSWORD),
            []
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteUnauthorized(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonDelete($client, '/users/1', [], []);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteForbidden(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonDelete(
            $client,
            '/users/1',
            [],
            $this->addBasicAuthorizationHeader(UserOne::USERNAME, UserOne::PASSWORD)
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPostWithMissingFields(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $response = $this->jsonPost($client, '/users', [], $this->addBasicAuthorizationHeader('admin', 'admin'), []);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
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
        $content = self::assertJsonResponse($response, Response::HTTP_CREATED);
        self::assertEquals([
            'id' => 3,
            'username' => 'username',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN']
        ], $content);
    }

    public function testPatch(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $user = $referenceRepository->getReference(UserOne::class, User::class);
        $response = $this->jsonPatch(
            $client,
            sprintf('/users/%d', $user->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
            [
                'username' => 'new-username',
                'password' => 'new-password',
                'admin' => false
            ]
        );
        $content = self::assertJsonResponse($response);
        self::assertEquals([
            'id' => $user->getId(),
            'username' => 'new-username',
            'roles' => ['ROLE_USER']
        ], $content);
    }

    public function testGet(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);
        $referenceRepository = self::loadFixtures([Users::class]);
        $user = $referenceRepository->getReference(UserOne::class, User::class);
        $response = $this->jsonGet(
            $client,
            sprintf('/users/%d', $user->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD),
        );
        $content = self::assertJsonResponse($response);
        self::assertEquals([
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ], $content);
    }

    public function testDelete(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([Users::class]);
        $user = $referenceRepository->getReference(UserOne::class, User::class);

        $id = $user->getId();

        $response = $this->jsonDelete(
            $client,
            sprintf('/users/%d', $id),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $response = $this->jsonGet(
            $client,
            sprintf('/users/%d', $id),
            [],
            $this->addBasicAuthorizationHeader(UserAdmin::USERNAME, UserAdmin::PASSWORD)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
