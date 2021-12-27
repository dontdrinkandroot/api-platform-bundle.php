<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Group;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\CrudOperation;
use ReflectionClass;
use RuntimeException;

class OperationGroupsMapper implements GroupsMapperInterface
{
    public const SEPARATOR = ':';

    const METHOD_LIST = 'list';
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    const OPERATION_ANY = 'ANY';
    const OPERATION_ANY_READ = 'ANY_READ';
    const OPERATION_ANY_WRITE = 'ANY_WRITE';

    const READ_METHODS = [self::METHOD_LIST, self::METHOD_GET];
    const WRITE_METHODS = [self::METHOD_POST, self::METHOD_PUT];

    const OPERATION_TYPE_COLLECTION = 'collection';
    const OPERATION_TYPE_ITEM = 'item';
    const OPERATION_TYPE_SUBRESOURCE = 'subresource';

    const ATTRIBUTE_OPERATION_TYPE = 'operation_type';
    const ATTRIBUTE_COLLECTION_OPERATION_NAME = 'collection_operation_name';
    const ATTRIBUTE_RESOURCE_CLASS = 'resource_class';
    const ATTRIBUTE_ITEM_OPERATION_NAME = 'item_operation_name';
    const ATTRIBUTE_SUBRESOURCE_RESOURCES = 'subresource_resources';
    const ATTRIBUTE_SUBRESOURCE_CONTEXT = '_api_subresource_context';

    public function __construct(private bool $enabled)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ApiRequest $apiRequest, bool $normalization): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function map(ApiRequest $apiRequest, bool $normalization): array
    {
        $method = null;
        $resourceClass = null;
        $subresourceProperty = null;
        $customOperationName = null;

        if ($apiRequest->isCollectionOperation()) {
            switch ($collectionOperation = $apiRequest->getCollectionOperation()) {
                case self::METHOD_GET:
                    $method = self::METHOD_LIST;
                    break;

                case self::METHOD_POST:
                    $method = $normalization ? self::METHOD_GET : self::METHOD_POST;
                    break;

                default:
                    $customOperationName = $collectionOperation;
                    $method = $normalization ? self::METHOD_GET : strtolower($apiRequest->getRequest()->getMethod());
            }
            $resourceClass = $this->getShortName($apiRequest->getResourceClass());
        } elseif ($apiRequest->isItemOperation()) {
            switch ($itemOperation = $apiRequest->getItemOperation()) {
                case self::METHOD_GET:
                    $method = self::METHOD_GET;
                    break;

                case self::METHOD_PUT:
                    $method = $normalization ? self::METHOD_GET : self::METHOD_PUT;
                    break;

                default:
                    $customOperationName = $itemOperation;
                    $method = $normalization ? self::METHOD_GET : strtolower($apiRequest->getRequest()->getMethod());
            }
            $resourceClass = $this->getShortName($apiRequest->getResourceClass());
        } elseif ($apiRequest->isSubresoureOperation()) {
            $method = $this->isSubresourceCollection($apiRequest) ? self::METHOD_LIST : self::METHOD_GET;
            $resourceClass = $this->getShortName($apiRequest->getSubresourceClass());
            $subresourceProperty = $this->getSubresourceProperty($apiRequest);
        }

        return $this->getGroups($method, $resourceClass, $subresourceProperty, $customOperationName);
    }

    private static function isReadMethod(string $method): bool
    {
        return in_array($method, self::READ_METHODS, true);
    }

    private function getShortName(string $class): string
    {
        $reflectedClass = new ReflectionClass($class);

        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $reflectedClass->getShortName()));
    }

    private function getSubresourceProperty(ApiRequest $request): string
    {
        $subResourceContext = $request->getRequest()->attributes->get(self::ATTRIBUTE_SUBRESOURCE_CONTEXT);

        return $subResourceContext['property'];
    }

    private function isSubresourceCollection(ApiRequest $request)
    {
        $subResourceContext = $request->getRequest()->attributes->get(self::ATTRIBUTE_SUBRESOURCE_CONTEXT);

        return $subResourceContext['collection'];
    }

    /**
     * @param array       $context
     * @param string      $method
     * @param string|null $resourceClass
     * @param string|null $subresourceProperty
     * @param string|null $customOperationName
     *
     * @return array
     */
    protected function getGroups(
        string $method,
        ?string $resourceClass,
        ?string $subresourceProperty,
        ?string $customOperationName
    ): array {
        $groupSuffix = $resourceClass;

        if (null !== $subresourceProperty) {
            $groupSuffix .= '.' . $subresourceProperty;
        }

        if (null !== $customOperationName) {
            $groupSuffix .= '.' . $customOperationName;
        }

        $crudOperation = $this->methodToCrudOperation($method);

        $groups = [];
        $groups[] = $crudOperation . self::SEPARATOR . $groupSuffix;
        $groups[] = self::OPERATION_ANY . self::SEPARATOR . $groupSuffix;

        if (self::isReadMethod($method)) {
            $groups[] = self::OPERATION_ANY_READ . self::SEPARATOR . $groupSuffix;
        } else {
            $groups[] = self::OPERATION_ANY_WRITE . self::SEPARATOR . $groupSuffix;
        }

        return $groups;
    }

    protected function methodToCrudOperation(string $method): string
    {
        switch ($method) {
            case self::METHOD_LIST:
                return CrudOperation::LIST;
            case self::METHOD_POST:
                return CrudOperation::CREATE;
            case self::METHOD_GET:
                return CrudOperation::READ;
            case self::METHOD_PUT:
                return CrudOperation::UPDATE;
            case self::METHOD_DELETE:
                return CrudOperation::DELETE;
        }

        throw new RuntimeException('Unknown method ' . $method);
    }
}
