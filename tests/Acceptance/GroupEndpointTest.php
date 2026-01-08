<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Group\GroupOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Group\GroupTwo;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserTwo;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Group;
use Symfony\Component\HttpFoundation\Response;

class GroupEndpointTest extends AbstractAcceptanceTestCase
{
    public function testReadProtected(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([GroupOne::class, GroupTwo::class]);
        $group2 = $referenceRepository->getReference(GroupTwo::class, Group::class);
        $response = $this->jsonGet(
            $client,
            sprintf("/groups/%d", $group2->getId()),
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $response = $this->jsonGet(
            $client,
            sprintf("/groups/%d", $group2->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserOne::USERNAME, UserOne::PASSWORD)
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testReadAccessibleForGroupMember(): void
    {
        $client = self::createClient();
        $referenceRepository = self::loadFixtures([GroupOne::class, GroupTwo::class]);
        $group2 = $referenceRepository->getReference(GroupTwo::class, Group::class);
        $response = $this->jsonGet(
            $client,
            sprintf("/groups/%d", $group2->getId()),
            [],
            $this->addBasicAuthorizationHeader(UserTwo::USERNAME, UserTwo::PASSWORD)
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
