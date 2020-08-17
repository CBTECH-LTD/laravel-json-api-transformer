<?php

namespace CbtechLtd\JsonApiTransformer;

use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CbtechLtd\JsonApiTransformer\JsonApiTransformer
 * @method static ApiResourceCollection collection(Collection $items)
 * @method static ApiResourceCollection collectionFromModels(string $resourceTypeClass, Collection $items)
 * @method static ApiResource single(string $resourceTypeClass, Model $model)
 */
class JsonApiTransformerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'json-api-transformer';
    }
}
