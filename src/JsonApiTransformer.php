<?php

namespace CbtechLtd\JsonApiTransformer;

use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class JsonApiTransformer
{
    public function collection(Collection $items): ApiResourceCollection
    {
        return ApiResourceCollection::make($items);
    }

    public function collectionFromModels(string $resourceTypeClass, Collection $models): ApiResourceCollection
    {
        Assert::isAOf(
            $resourceTypeClass,
            ResourceType::class,
            'First parameter must be a ' . ResourceType::class
        );

        $items = $models->map(fn(Model $model) => new $resourceTypeClass($model));

        return $this->collection($items);
    }

    public function single(string $resourceTypeClass, Model $model): ApiResource
    {
        Assert::isAOf(
            $resourceTypeClass,
            ResourceType::class,
            'First parameter must be a ' . ResourceType::class
        );

        return ApiResource::make(new $resourceTypeClass($model));
    }
}
