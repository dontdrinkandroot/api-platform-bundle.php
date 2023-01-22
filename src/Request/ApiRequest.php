<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Request;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Dontdrinkandroot\Common\CrudOperation;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class that eases the handling with Api Platform request attributes.
 */
class ApiRequest
{
    private const METHOD_GET = 'GET';
    private const METHOD_POST = 'POST';
    private const METHOD_PUT = 'PUT';
    private const METHOD_DELETE = 'DELETE';

    const ATTRIBUTE_ID = 'id';
    const ATTRIBUTE_DATA = 'data';
    public const ATTRIBUTE_API_OPERATION = '_api_operation';
    const ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';
    const ATTRIBUTE_API_COLLECTION_OPERATION_NAME = '_api_collection_operation_name';
    const ATTRIBUTE_API_ITEM_OPERATION_NAME = '_api_item_operation_name';
    const ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME = '_api_subresource_operation_name';
    const ATTRIBUTE_API_SUBRESOURCE_CONTEXT = '_api_subresource_context';
    const ATTRIBUTE_ROUTE = '_route';

    public function __construct(private readonly Request $request)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string|null
     * @deprecated We cannot predict the field here.
     */
    public function getId(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_ID);
    }

    public function getData(): mixed
    {
        return $this->request->attributes->get(self::ATTRIBUTE_DATA);
    }

    /**
     * @return class-string|null
     */
    public function getResourceClass(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_RESOURCE_CLASS);
    }

    public function getCollectionOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_COLLECTION_OPERATION_NAME);
    }

    public function isCollectionOperation(): bool
    {
        return null !== $this->getCollectionOperation();
    }

    public function getItemOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_ITEM_OPERATION_NAME);
    }

    public function isItemOperation(): bool
    {
        return null !== $this->getItemOperation();
    }

    public function getSubresourceOperation(): ?string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME);
    }

    public function isSubresoureOperation(): bool
    {
        return null !== $this->getSubresourceOperation();
    }

    /**
     * @param class-string $resourceClass
     * Checks if the Request handles the given resource class.
     */
    public function handlesResourceClass(string $resourceClass, bool $checkDataClass = false): bool
    {
        if ($resourceClass !== $this->getResourceClass()) {
            return false;
        }

        if ($checkDataClass && null !== $this->getData()) {
            return is_a($this->getData(), $resourceClass);
        }

        return true;
    }

    /**
     * Checks if the Request handles one of the given resource classes
     */
    public function handlesOneOfTheResourceClasses(array $resourceClasses, bool $checkDataClass = false): bool
    {
        foreach ($resourceClasses as $resourceClass) {
            if ($this->handlesResourceClass($resourceClass, $checkDataClass)) {
                return true;
            }
        }

        return false;
    }

    public function getRoute(): string
    {
        return $this->request->attributes->get(self::ATTRIBUTE_ROUTE);
    }

    public function handlesRoute(string $route): bool
    {
        return $this->getRoute() === $route;
    }

    public function handlesOneOfTheRoutes(array $routes): bool
    {
        foreach ($routes as $route) {
            if ($this->handlesRoute($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the Request is a create or update operation for the given resource class.
     *
     * @param class-string|null $resourceClass
     */
    public function isCreateOrUpdate(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        return $this->isCollectionPost($resourceClass, $checkDataClass)
            || $this->isItemPut($resourceClass, $checkDataClass);
    }

    /**
     * @param class-string|null $resourceClass
     *
     * @return bool
     */
    public function isCollectionGet(string $resourceClass = null): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass)) {
            return false;
        }

        return $this->getOperation() instanceof GetCollection;
    }

    /**
     * @param class-string|null $resourceClass
     * @param bool $checkDataClass
     *
     * @return bool
     */
    public function isCollectionPost(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Post;
    }

    /**
     * @param class-string|null $resourceClass
     * @param bool $checkDataClass
     *
     * @return bool
     */
    public function isItemGet(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Get;
    }

    /**
     * @param class-string|null $resourceClass
     * @param bool $checkDataClass
     *
     * @return bool
     */
    public function isItemPut(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Put;
    }

    /**
     * @param class-string|null $resourceClass
     * @param bool $checkDataClass
     *
     * @return bool
     */
    public function isItemDelete(string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Delete;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->request->attributes->get($key);
    }

    /**
     * @template T
     *
     * @return class-string<T>|null
     */
    public function getSubresourceContextClass(): ?string
    {
        if ($this->request->attributes->has(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT)) {
            $subresourceContext = $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT);

            $numIdentifiers = count($subresourceContext['identifiers']);
            if (0 === $numIdentifiers) {
                throw new RuntimeException('No identifier found');
            }
            if ($numIdentifiers > 1) {
                throw new RuntimeException('Too many identifiers');
            }

            return reset($subresourceContext['identifiers'])[0];
        }

        return null;
    }

    public function getSubresourceContextProperty(): ?string
    {
        if ($this->request->attributes->has(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT)) {
            $subresourceContext = $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT);
            return $subresourceContext['property'];
        }
        return null;
    }

    public function isSubresourceContextCollection(): bool
    {
        if ($this->request->attributes->has(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT)) {
            $subresourceContext = $this->request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_CONTEXT);
            return $subresourceContext['collection'];
        }
        return false;
    }

    public function getCrudOperation(): ?CrudOperation
    {
        if ($this->isCollectionGet()) {
            return CrudOperation::LIST;
        }

        if ($this->isItemGet()) {
            return CrudOperation::READ;
        }

        if ($this->isCollectionPost()) {
            return CrudOperation::CREATE;
        }

        if ($this->isItemPut()) {
            return CrudOperation::UPDATE;
        }

        if ($this->isItemDelete()) {
            return CrudOperation::DELETE;
        }

        return null;
    }

    public function getOperation(): ?Operation
    {
        return $this->request->attributes->get(self::ATTRIBUTE_API_OPERATION);
    }
}
