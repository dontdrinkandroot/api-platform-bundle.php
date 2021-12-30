<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Attribute;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\CrudOperation;

abstract class AbstractCrudOperationAttributesMapper implements AttributesMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        $crudOperation = $apiRequest->getCrudOperation();
        if (null !== $crudOperation) {
            if ($normalization && in_array(
                    $crudOperation,
                    [CrudOperation::CREATE, CrudOperation::UPDATE, CrudOperation::DELETE],
                    true
                )) {
                return $this->mapCrudOperation(CrudOperation::READ, $apiRequest);
            }

            return $this->mapCrudOperation($crudOperation, $apiRequest);
        }

        return $this->mapOther($apiRequest, $normalization);
    }

    protected function mapCrudOperation(string $crudOperation, ApiRequest $apiRequest): array
    {
        return [];
    }

    protected function mapOther(ApiRequest $apiRequest, bool $normalization): array
    {
        return [];
    }
}
