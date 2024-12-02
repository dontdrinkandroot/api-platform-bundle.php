<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Serializer;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute\AttributesMapperInterface;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Override;

class UserAttributesMapper implements AttributesMapperInterface
{
    #[Override]
    public function supports(ApiRequest $apiRequest, bool $normalization): bool
    {
        return $apiRequest->handlesResourceClass(User::class);
    }

    #[Override]
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        if (!$normalization && $apiRequest->isCollectionPost(User::class)) {
            return ['department', 'username', 'password', 'admin'];
        }

        return ['id', 'username', 'roles'];
    }
}
