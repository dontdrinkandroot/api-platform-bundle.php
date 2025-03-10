<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Request;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Dontdrinkandroot\Common\CrudOperation;
use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class that eases the handling with Api Platform request attributes.
 */
class ApiRequest
{
    final const string ATTRIBUTE_ID = 'id';
    final const string ATTRIBUTE_DATA = 'data';
    final const string ATTRIBUTE_PREVIOUS_DATA = 'previous_data';
    final const string ATTRIBUTE_API_OPERATION = '_api_operation';
    final const string ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';
    final const string ATTRIBUTE_ROUTE = '_route';
    final const string ATTRIBUTE_API_HAS_COMPOSITE_IDENTIFIER = '_api_has_composite_identifier';
    final const string ATTRIBUTE_API_OPERATION_NAME = '_api_operation_name';

    public function __construct(public readonly Request $request)
    {
    }

    public function hasCompositeIdentifier(): bool
    {
        return $this->getAttribute(self::ATTRIBUTE_API_HAS_COMPOSITE_IDENTIFIER) ?? false;
    }

    /**
     * @return class-string|null
     */
    public function getResourceClass(): ?string
    {
        return $this->getAttribute(self::ATTRIBUTE_API_RESOURCE_CLASS);
    }

    public function getOperationName(): ?string
    {
        return $this->getAttribute(self::ATTRIBUTE_API_OPERATION_NAME);
    }

    public function getOperation(): ?Operation
    {
        return $this->getAttribute(self::ATTRIBUTE_API_OPERATION);
    }

    public function getData(): mixed
    {
        return $this->getAttribute(self::ATTRIBUTE_DATA);
    }

    public function getPreviousData(): mixed
    {
        return $this->getAttribute(self::ATTRIBUTE_PREVIOUS_DATA);
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
     * Checks if the Request handles one of the given resource classes.
     *
     * @param class-string[] $resourceClasses
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
        return $this->getAttribute(self::ATTRIBUTE_ROUTE);
    }

    public function handlesRoute(string $route): bool
    {
        return $this->getRoute() === $route;
    }

    /**
     * @param string[] $routes
     */
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
    public function isCreateOrUpdate(?string $resourceClass = null, bool $checkDataClass = true): bool
    {
        return $this->isCollectionPost($resourceClass, $checkDataClass)
            || $this->isItemPut($resourceClass, $checkDataClass);
    }

    /**
     * @param class-string|null $resourceClass
     */
    public function isCollectionGet(?string $resourceClass = null): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass)) {
            return false;
        }

        return $this->getOperation() instanceof GetCollection;
    }

    /**
     * @param class-string|null $resourceClass
     *
     */
    public function isCollectionPost(?string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Post;
    }

    /**
     * @param class-string|null $resourceClass
     *
     */
    public function isItemGet(?string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Get;
    }

    /**
     * @param class-string|null $resourceClass
     *
     */
    public function isItemPut(?string $resourceClass = null, bool $checkDataClass = true): bool
    {
        if (null !== $resourceClass && !$this->handlesResourceClass($resourceClass, $checkDataClass)) {
            return false;
        }

        return $this->getOperation() instanceof Put;
    }

    /**
     * @param class-string|null $resourceClass
     *
     */
    public function isItemDelete(?string $resourceClass = null, bool $checkDataClass = true): bool
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
}
