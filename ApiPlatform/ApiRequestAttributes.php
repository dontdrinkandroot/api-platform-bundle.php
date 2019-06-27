<?php

namespace Dontdrinkandroot\ApiPlatformBundle\ApiPlatform;

use Symfony\Component\HttpFoundation\Request;

/**
 * Helper class that eases the handling with Api Platform request attributes.
 *
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class ApiRequestAttributes
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    const ATTRIBUTE_ID = 'id';
    const ATTRIBUTE_DATA = 'data';
    const ATTRIBUTE_API_RESOURCE_CLASS = '_api_resource_class';
    const ATTRIBUTE_API_COLLECTION_OPERATION_NAME = '_api_collection_operation_name';
    const ATTRIBUTE_API_ITEM_OPERATION_NAME = '_api_item_operation_name';
    const ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME = '_api_subresource_operation_name';
    const ATTRIBUTE_ROUTE = '_route';

    private $id;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string|null
     */
    private $resourceClass;

    /**
     * @var string|null
     */
    private $collectionOperation;

    /**
     * @var string|null
     */
    private $itemOperation;

    /**
     * @var string|null
     */
    private $subresourceOperation;

    /**
     * @var string
     */
    private $route;

    public static function extract(Request $request): ApiRequestAttributes
    {
        $attributes = new ApiRequestAttributes();
        $attributes->id = $request->attributes->get(self::ATTRIBUTE_ID);
        $attributes->data = $request->attributes->get(self::ATTRIBUTE_DATA);
        $attributes->resourceClass = $request->attributes->get(self::ATTRIBUTE_API_RESOURCE_CLASS);
        $attributes->collectionOperation = $request->attributes->get(self::ATTRIBUTE_API_COLLECTION_OPERATION_NAME);
        $attributes->itemOperation = $request->attributes->get(self::ATTRIBUTE_API_ITEM_OPERATION_NAME);
        $attributes->subresourceOperation = $request->attributes->get(self::ATTRIBUTE_API_SUBRESOURCE_OPERATION_NAME);
        $attributes->route = $request->attributes->get(self::ATTRIBUTE_ROUTE);

        return $attributes;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return null|string
     */
    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    /**
     * @return null|string
     */
    public function getCollectionOperation(): ?string
    {
        return $this->collectionOperation;
    }

    /**
     * @return null|string
     */
    public function getItemOperation(): ?string
    {
        return $this->itemOperation;
    }

    /**
     * @return null|string
     */
    public function getSubresourceOperation(): ?string
    {
        return $this->subresourceOperation;
    }

    /**
     * Checks if the Request handles the given resource class.
     */
    public function handlesResourceClass(string $resourceClass): bool
    {
        return $resourceClass === $this->resourceClass;
    }

    /**
     * Checks if the Request handles one of the given resource classes
     */
    public function handlesOneResourceClass(array $resourceClasses): bool
    {
        foreach ($resourceClasses as $resourceClass) {
            if ($this->handlesResourceClass($resourceClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
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
     */
    public function isCreateOrUpdate(string $resourceClass): bool
    {
        return $this->handlesData($resourceClass) && ($this->isCollectionPost() || $this->isItemPut());
    }

    public function handlesData(string $resourceClass): bool
    {
        return $this->handlesResourceClass($resourceClass) && is_a($this->getData(), $resourceClass);
    }

    public function isCollectionGet(): bool
    {
        return self::METHOD_GET === $this->collectionOperation;
    }

    public function isCollectionPost(): bool
    {
        return self::METHOD_POST === $this->collectionOperation;
    }

    public function isItemGet(): bool
    {
        return self::METHOD_GET === $this->itemOperation;
    }

    public function isItemPut(): bool
    {
        return self::METHOD_PUT === $this->itemOperation;
    }
}
