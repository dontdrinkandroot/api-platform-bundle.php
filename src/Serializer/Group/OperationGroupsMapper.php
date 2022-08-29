<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Serializer\Group;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\Asserted;
use Dontdrinkandroot\Common\CrudOperation;
use ReflectionClass;
use ReflectionException;

class OperationGroupsMapper implements GroupsMapperInterface
{
    public const OPERATION_ANY = 'ANY';
    public const OPERATION_ANY_READ = 'ANY_READ';
    public const OPERATION_ANY_WRITE = 'ANY_WRITE';

    private const SEPARATOR = ':';

    private const METHOD_LIST = 'list';
    private const METHOD_GET = 'get';
    private const METHOD_POST = 'post';
    private const METHOD_PUT = 'put';
    private const METHOD_DELETE = 'delete';

    private const READ_METHODS = [self::METHOD_LIST, self::METHOD_GET];

    private const ATTRIBUTE_SUBRESOURCE_CONTEXT = '_api_subresource_context';

    public function __construct(private readonly bool $enabled)
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
            [$resourceClass, $method, $customOperationName, $subresourceProperty] = $this->mapCollectionOperation(
                $apiRequest,
                $normalization,
                $customOperationName
            );
        } elseif ($apiRequest->isItemOperation()) {
            [$resourceClass, $method, $customOperationName, $subresourceProperty] = $this->mapItemOperation(
                $apiRequest,
                $normalization,
                $customOperationName
            );
        } elseif ($apiRequest->isSubresoureOperation()) {
            [$resourceClass, $method, $customOperationName, $subresourceProperty] = $this->mapSubresourceOperation(
                $apiRequest
            );
        }

        return $this->getGroups(
            Asserted::notNull($method, 'No method resolved'),
            Asserted::notNull($resourceClass, 'No resourceClass resolved'),
            $subresourceProperty,
            $customOperationName
        );
    }

    private static function isReadMethod(string $method): bool
    {
        return in_array($method, self::READ_METHODS, true);
    }

    /**
     * @param class-string $class
     *
     * @return string
     * @throws ReflectionException
     */
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

    private function isSubresourceCollection(ApiRequest $request): bool
    {
        $subResourceContext = $request->getRequest()->attributes->get(self::ATTRIBUTE_SUBRESOURCE_CONTEXT);

        return $subResourceContext['collection'];
    }

    /**
     * @param string       $method
     * @param class-string $resourceClass
     * @param string|null  $subresourceProperty
     * @param string|null  $customOperationName
     *
     * @return array
     */
    protected function getGroups(
        string $method,
        string $resourceClass,
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

        $groups = [];
        $groups[] = $this->translateMethod($method) . self::SEPARATOR . $groupSuffix;
        $groups[] = self::OPERATION_ANY . self::SEPARATOR . $groupSuffix;

        if (self::isReadMethod($method)) {
            $groups[] = self::OPERATION_ANY_READ . self::SEPARATOR . $groupSuffix;
        } else {
            $groups[] = self::OPERATION_ANY_WRITE . self::SEPARATOR . $groupSuffix;
        }

        return $groups;
    }

    protected function translateMethod(string $method): string
    {
        return match ($method) {
            self::METHOD_LIST => CrudOperation::LIST->value,
            self::METHOD_GET => CrudOperation::READ->value,
            self::METHOD_POST => CrudOperation::CREATE->value,
            self::METHOD_PUT => CrudOperation::UPDATE->value,
            self::METHOD_DELETE => CrudOperation::DELETE->value,
            default => $method,
        };
    }

    public function mapCollectionOperation(
        ApiRequest $apiRequest,
        bool $normalization,
        ?string $customOperationName
    ): array {
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
        $resourceClass = $this->getShortName(Asserted::notNull($apiRequest->getResourceClass()));
        return [$resourceClass, $method, $customOperationName, null];
    }

    public function mapItemOperation(ApiRequest $apiRequest, bool $normalization, ?string $customOperationName): array
    {
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
        $resourceClass = $this->getShortName(Asserted::notNull($apiRequest->getResourceClass()));

        return [$resourceClass, $method, $customOperationName, null];
    }

    public function mapSubresourceOperation(ApiRequest $apiRequest): array
    {
        $method = $this->isSubresourceCollection($apiRequest) ? self::METHOD_LIST : self::METHOD_GET;
        $resourceClass = $this->getShortName(Asserted::notNull($apiRequest->getSubresourceContextClass()));
        $subresourceProperty = $this->getSubresourceProperty($apiRequest);

        return [$resourceClass, $method, null, $subresourceProperty];
    }
}
