<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Serializer;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute\AttributesMapperInterface;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Override;

class DepartmentAttributesMapper implements AttributesMapperInterface
{
    #[Override]
    public function supports(ApiRequest $apiRequest, bool $normalization): bool
    {
        return $apiRequest->handlesResourceClass(Department::class);
    }

    #[Override]
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        if (!$normalization && $apiRequest->isCollectionPost(User::class)) {
            return ['name'];
        }

        return ['id', 'name'];
    }
}
